<html lang="en"><script src="chrome-extension://eppiocemhmnlbhjplcgkofciiegomcon/content/location/location.js" id="eppiocemhmnlbhjplcgkofciiegomcon"></script><script src="chrome-extension://eppiocemhmnlbhjplcgkofciiegomcon/libs/extend-native-history-api.js"></script><script src="chrome-extension://eppiocemhmnlbhjplcgkofciiegomcon/libs/requests.js"></script><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="r6HllrlTWyA6Gikm7c5g1qQt2JZJjgThuZvSRkCZ">

    <title>ใบสั่งขาย - 3E</title>

    <!-- Scripts -->
    <script src="http://server_update:8000/js/app.js" defer=""></script>


    <!-- Styles -->
    <style>
        #navHeaderMenu {
            list-style: none inside;
            margin: 0;
            padding: 0;
            text-align: center;
            z-index: 999999;
            position: relative;
        }

        #navHeaderMenu li {
            display: block;
            position: relative;
            float: left;
            background: #fff;
            /* menu background color */
        }

        #navHeaderMenu li a {
            display: block;
            padding: 0;
            text-decoration: none;
            width: 200px;
            /* this is the width of the menu items */
            line-height: 35px;
            /* this is the hieght of the menu items */
            color: #000;
            /* list item font color */
        }

        #navHeaderMenu li li a {
            font-size: 100%;
        }

        /* smaller font size for sub menu items */
        #navHeaderMenu li:hover {
            background: #c7cac9;
        }

        #navHeaderMenu li: {
            background: #003f20;
        }

        /* highlights current hovered list item and the parent list items when hovering over sub menues */
        #navHeaderMenu ul {
            position: absolute;
            padding: 0;
            left: 0;
            display: none;
            /* hides sublists */
        }

        #navHeaderMenu li:hover ul ul {
            display: none;
        }

        /* hides sub-sublists */
        #navHeaderMenu li:hover ul {
            display: block;
        }

        /* shows sublist on hover */
        #navHeaderMenu li li:hover ul {
            display: block;
            /* shows sub-sublist on hover */
            margin-left: 200px;
            /* this should be the same width as the parent list item */
            margin-top: -35px;
            /* aligns top of sub menu with top of list item */
        }
    </style>
<style>
    .custom-dropdown {
        position: relative;
        display: inline-block;
    }

    /* ปุ่มกดขนาดเล็ก */
    .compact-dropbtn {
        background-color: #f8f9fa;
        color: #4b5563;
        padding: 8px 16px;
        font-size: 14px;
        font-weight: 500;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .compact-dropbtn:hover {
        background-color: #ffffff;
        border-color: #3b82f6;
        color: #3b82f6;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    /* สไตล์เมื่อ dropdown เปิดอยู่ */
    .custom-dropdown.active .compact-dropbtn {
        background-color: #ffffff;
        border-color: #3b82f6;
        color: #3b82f6;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .arrow-icon {
        font-size: 10px;
        margin-left: 4px;
        transition: transform 0.3s ease;
    }

    /* หมุนลูกศรเมื่อเปิดเมนู */
    .custom-dropdown.active .arrow-icon {
        transform: rotate(180deg);
    }

    /* กล่องรายการเมนู */
    .compact-dropdown-content {
        display: none;
        position: absolute;
        right: 0;
        background-color: #ffffff;
        min-width: 200px;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        z-index: 1000;
        margin-top: 5px;
        overflow: hidden;
    }

    /* แสดงเมื่อมีคลาส show */
    .compact-dropdown-content.show {
        display: block;
    }

    /* รายการลิงก์ข้างใน */
    .compact-dropdown-content a {
        color: #374151;
        padding: 10px 16px;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        transition: background 0.2s;
    }

    .compact-dropdown-content a i {
        width: 18px;
        text-align: center;
        color: #6b7280;
    }

    .compact-dropdown-content a:hover {
        background-color: #eff6ff;
        color: #2563eb;
    }

    .compact-dropdown-content a:hover i {
        color: #2563eb;
    }
</style>
        <style>
        .tooltipDate {
            position: relative;
            display: inline-block;
            border-bottom: 1px dotted black;
        }

        .tooltipDate .tooltiptextDate {
            visibility: hidden;
            width: 120px;
            background-color: black;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px 0;
            font-size: 10px;

            /* Position the tooltip */
            position: absolute;
            z-index: 1;
        }

        .tooltipDate:hover .tooltiptextDate {
            visibility: visible;
        }

        .previous {
            background-color: #f1f1f1;
            color: black;
        }

        .round {
            border-radius: 50%;
        }

    </style>
    <link href="http://server_update:8000/css/app.css" rel="stylesheet">
        <script>
        async function getSOHD(SONum) {
            const response = await fetch("http://server_update:8000/api/getSOHD?SONum=" + SONum)
            if (response.ok) {
                let SONums = await response.json()
                SONums.forEach((_SONum) => {
                    console.log('td' + _SONum.SONum)
                    document.getElementById('td' + _SONum.SONum).innerHTML = _SONum.DocuStatus
                })
            }
        }
    </script>
<script bis_use="true" type="text/javascript" charset="utf-8" nonce="" data-dynamic-id="a5ca7aea-af6f-4f21-a09c-2d428d6cb590" src="chrome-extension://eppiocemhmnlbhjplcgkofciiegomcon/executors/200.js"></script><script bis_use="true" type="text/javascript" charset="utf-8" nonce="" data-dynamic-id="a5ca7aea-af6f-4f21-a09c-2d428d6cb590" src="chrome-extension://eppiocemhmnlbhjplcgkofciiegomcon/executors/101.js"></script></head>

<body bis_register="W3sibWFzdGVyIjp0cnVlLCJleHRlbnNpb25JZCI6ImVwcGlvY2VtaG1ubGJoanBsY2drb2ZjaWllZ29tY29uIiwiYWRibG9ja2VyU3RhdHVzIjp7IkRJU1BMQVkiOiJlbmFibGVkIiwiRkFDRUJPT0siOiJlbmFibGVkIiwiVFdJVFRFUiI6ImVuYWJsZWQiLCJSRURESVQiOiJlbmFibGVkIiwiUElOVEVSRVNUIjoiZW5hYmxlZCIsIklOU1RBR1JBTSI6ImVuYWJsZWQiLCJUSUtUT0siOiJlbmFibGVkIiwiTElOS0VESU4iOiJlbmFibGVkIiwiQ09ORklHIjoiZGlzYWJsZWQifSwidmVyc2lvbiI6IjIuMS4yIiwic2NvcmUiOjIwMTAyMH1d" __processed_fd209db0-9f83-479e-b8ed-d2384f135488__="true">
    <div id="app" bis_skin_checked="1"><nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm"><a href="javascript:history.back()"><img src="http://server_update:8000/images/left-arrow.png" style="width: 30px; height: 30px;"></a> <div class="container" bis_skin_checked="1"><a href="http://server_update:8000" class="navbar-brand"><img src="http://server_update:8000/images/LOGO_3E.jpg" style="width: 50px; height: 50px;"></a> <ul id="navHeaderMenu"><li><a href="javascript:void(0)"><button type="button" class="btn btn-secondary dropdown-toggle" style="width: 100%; background: rgb(233, 105, 75);">MENU</button></a> <ul><li><a href="http://server_update:8000/solist">SO</a></li><li><a href="/disbursement">เบิกจ่าย</a></li><li><a href="/bill_requirement">ใบ Requirement</a></li></ul></li></ul> <a href="http://127.0.0.1:8000/soitem?create_by=test101" class="btn shadow-sm" style="margin-left: 20px; background: rgb(255, 247, 237); color: rgb(234, 88, 12); padding: 7px 16px; font-size: 13px; font-weight: 600; border-radius: 6px;"><i class="fa-solid fa-file-invoice me-2"></i>
                        Quotation
                    </a> <button type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler"><span class="navbar-toggler-icon"></span></button> <div id="navbarSupportedContent" class="collapse navbar-collapse" bis_skin_checked="1"><ul class="navbar-nav mr-auto"></ul> <ul class="navbar-nav ml-auto"><a href="http://server_update:8000/admin/dashboard"><li class="nav-link">admin panel</li></a> <li class="nav-item dropdown"><a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    test101
                                </a> <div aria-labelledby="navbarDropdown" class="dropdown-menu dropdown-menu-right" bis_skin_checked="1"><a href="http://server_update:8000/logout" onclick="event.preventDefault();
                                                                             document.getElementById('logout-form').submit();" class="dropdown-item">
                                        Logout
                                    </a> <form id="logout-form" action="http://server_update:8000/logout" method="POST" class="d-none"><input type="hidden" name="_token" value="r6HllrlTWyA6Gikm7c5g1qQt2JZJjgThuZvSRkCZ"></form></div></li></ul></div> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> <div class="custom-dropdown" bis_skin_checked="1"><button type="button" onclick="toggleDropdown(event)" class="compact-dropbtn"><i class="fa-solid fa-bars-staggered"></i> เมนู
        <i class="fa-solid fa-chevron-down arrow-icon"></i></button> <div id="myDropdown" class="compact-dropdown-content" bis_skin_checked="1"><a href="http://127.0.0.1:8000/dashboardtechnician?create_by=test101"><i class="fa-solid fa-screwdriver-wrench"></i> Projects
        </a> <a href="http://127.0.0.1:8000/oil?create_by=test101"><i class="fa-solid fa-screwdriver-wrench"></i>ระบบน้ำมัน
        </a> <a href="http://127.0.0.1:8000/adminOT?create_by=test101"><i class="fa-solid fa-business-time"></i> จัดการ OT
        </a><a href="http://127.0.0.1:8000/admin?create_by=test101"><i class="fa-solid fa-screwdriver-wrench"></i>inventory
        </a></div></div></div></nav></div>
    <script>
    function toggleDropdown(event) {
        event.stopPropagation(); // กัน event ลามไป document
        const dropdown = event.currentTarget.parentElement;
        const content = dropdown.querySelector('.compact-dropdown-content');
        
        // ปิด dropdown อื่นๆ ทั้งหมดก่อน (กรณีมีหลายอัน)
        document.querySelectorAll('.compact-dropdown-content.show').forEach(item => {
            if (item !== content) {
                item.classList.remove('show');
                item.parentElement.classList.remove('active');
            }
        });

        // toggle อันที่กด
        content.classList.toggle('show');
        dropdown.classList.toggle('active');
    }

    // คลิกที่อื่นเพื่อปิด dropdown
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.custom-dropdown')) {
            document.querySelectorAll('.compact-dropdown-content.show').forEach(item => {
                item.classList.remove('show');
                item.parentElement.classList.remove('active');
            });
        }
    });
</script>
    <div class="container-fluid" bis_skin_checked="1">
        <main class="py-3">
            
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .so-search-wrapper {
            display: flex;
            gap: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px 20px;
            background: #fff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            max-width: 950px;
            margin: 0 auto;
            align-items: stretch;
        }

        /* Sidebar */
        .so-sidebar {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 145px;
            padding-right: 18px;
            border-right: 1px solid #eef0f3;
            align-self: stretch;
        }

        .so-sidebar .so-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            flex: 1;
            text-align: left;
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: filter 0.15s ease, transform 0.05s ease;
            line-height: 1.2;
        }

        .so-sidebar .so-btn:hover  { filter: brightness(0.96); text-decoration:none; }
        .so-sidebar .so-btn:active { transform: translateY(1px); }

        .so-btn-create   { background:#d1f0db; color:#1f7a3d !important; }
        .so-btn-billing  { background:#d8e6ff; color:#1e40af !important; }
        .so-btn-potrack  { background:#e4daff; color:#5b21b6 !important; }
        .so-btn-issues   { background:#fcdcdc; color:#b91c1c !important; }
        .so-btn-deposit  { background:#fde9b0; color:#92400e !important; }

        .so-sidebar .so-btn i {
            font-size: 12px;
            width: 14px;
            text-align: center;
        }

        /* Form grid */
        .so-form-grid {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px 14px;
            align-items: center;
        }

        .so-form-grid .form-control {
            width: 100%;
            height: 34px;
            padding: 6px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            background: #fff;
            font-size: 13px;
            color: #374151;
            box-sizing: border-box;
            box-shadow: none;
        }

        .so-form-grid .form-control::placeholder { color: #9ca3af; opacity: 1; font-size: 13px; }
        .so-form-grid .form-control:focus {
            outline: none;
            border-color: #93c5fd;
            box-shadow: 0 0 0 2px rgba(147, 197, 253, 0.2);
        }

        .so-form-grid select.form-control {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%236b7280'%3e%3cpath d='M4.5 6l3.5 3.5L11.5 6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 12px;
            padding-right: 28px;
        }

        .so-form-grid input[type="date"].form-control {
            color: #9ca3af;
            font-family: inherit;
        }
        .so-form-grid input[type="date"].form-control:valid,
        .so-form-grid input[type="date"].form-control:focus {
            color: #374151;
        }

        .so-delay-cell {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            height: 34px;
            color: #374151;
        }

        .so-delay-cell input[type="checkbox"] {
            width: 14px;
            height: 14px;
            cursor: pointer;
            margin: 0;
        }

        .so-delay-cell label {
            margin: 0;
            cursor: pointer;
        }

        .so-action-row {
            margin-top: 12px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            max-width: 950px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Search & Reset buttons */
        .so-search-cell {
            display: flex;
            align-items: center;
            justify-content: stretch;
            gap: 8px;
            height: 34px;
        }

        .btn-search {
            color: #2563eb !important;
            background: #fff;
            border: 1px solid #2563eb;
            border-radius: 4px;
            padding: 0 18px;
            font-weight: 600;
            font-size: 13px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            height: 34px;
            box-sizing: border-box;
            white-space: nowrap;
            flex: 1;
        }
        .btn-search i {
            font-size: 13px;
        }

        .btn-reset {
            color: #dc2626 !important;
            background: #fff;
            border: 1px solid #dc2626;
            border-radius: 4px;
            padding: 0 18px;
            font-weight: 600;
            font-size: 13px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            height: 34px;
            box-sizing: border-box;
            white-space: nowrap;
            flex: 1;
        }
        .btn-reset i {
            font-size: 13px;
        }
    </style>

    <div class="container" style="max-width: 1000px;" bis_skin_checked="1">

        <form action="http://server_update:8000/solist" method="GET" id="searchForm">
            <input type="hidden" name="_token" value="r6HllrlTWyA6Gikm7c5g1qQt2JZJjgThuZvSRkCZ">
            <div class="so-search-wrapper" bis_skin_checked="1">
                
                <div class="so-sidebar" bis_skin_checked="1">
                    <a href="http://server_update:8000/create_so" class="so-btn so-btn-create" title="Create SO">
                        <i class="fa-solid fa-file-circle-plus"></i>
                        <span>Create SO</span>
                    </a>

                    <a href="http://127.0.0.1:8000/dashboard?create_by=test101" class="so-btn so-btn-billing" title="ข้อมูลจัดส่ง">
                        <i class="fa-solid fa-credit-card"></i>
                        <span>Billing</span>
                    </a>

                    <a href="http://127.0.0.1:8000/pooutside?create_by=test101" class="so-btn so-btn-potrack" title="ติดตามคำสั่งซื้อPO">
                        <i class="fa-solid fa-ship"></i>
                        <span>PO Tracking</span>
                    </a>

                    <a href="http://127.0.0.1:8000/deliverytrack?create_by=test101" class="so-btn so-btn-issues" title="ข้อมูลของผิด">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Issues</span>
                    </a>

                    <a href="http://127.0.0.1:8000/dashboarddeposit?create_by=test101" class="so-btn so-btn-deposit" title="Deposit Invoice">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        <span>Deposit Invoice</span>
                    </a>
                </div>

                
                <div class="so-form-grid" bis_skin_checked="1">
                    
                    <input class="form-control" placeholder="SO" onkeypress="submitByEnter(event)" name="SONum" type="text">
                    <input class="form-control" placeholder="รหัสลูกค้า" onkeypress="submitByEnter(event)" name="CustomerCode" type="text">
                    <input class="form-control" placeholder="ชื่อลูกค้า" onkeypress="submitByEnter(event)" name="CustomerName" type="text">

                    
                    <input class="form-control" placeholder="PO" onkeypress="submitByEnter(event)" name="PONum" type="text">

                    <select name="SOStatus" id="SOStatus" class="form-control">
                        <option value="">สถานะ</option>
                        <option value="SO_ENTRY" style="background-color:#FF99FF;">เปิด PO ไม่ครบ</option>
                        <option value="RECEIPT_PARTIAL" style="background-color:yellow;">ของมาบางส่วน</option>
                        <option value="RECEIPT_ALL" style="background-color:green;">ของมาครบ</option>
                        <option value="SO_COMPLETED" style="background-color:white;">สมบูรณ์</option>
                        <option value="SO_NO_COMPLETE" style="background-color:rgb(224, 77, 77);">ไม่ COMPLETE</option>
                    </select>

                    <div class="so-delay-cell" bis_skin_checked="1">
                        <input id="delaySO" name="delaySO" type="checkbox" value="Y">
                        <label for="delaySO">SO เลยกำหนด</label>
                    </div>

                    
                    <input class="form-control" placeholder="Bill" onkeypress="submitByEnter(event)" name="BillNum" type="text">
                    <input class="form-control" placeholder="PO อ้างอิง" onkeypress="submitByEnter(event)" name="CustPONo" type="text">
                    <div class="so-search-cell" bis_skin_checked="1">
                        <a href="javascript:void(0)" onclick="submitForm('search')" class="btn-search">
                            <i class="fa-solid fa-magnifying-glass"></i> Search
                        </a>
                        <a href="javascript:void(0)" onclick="resetCriteria()" class="btn-reset">
                            <i class="fa-solid fa-rotate-left"></i> Reset
                        </a>
                    </div>

                    
                    <select class="form-control" name="ResponseBy"><option value="" selected="selected">ผู้รับผิดชอบ</option><option value="0"></option><option value="am3e">am3e</option><option value="Aor">Aor</option><option value="ARTEE3E">ARTEE3E</option><option value="Atitaya">Atitaya</option><option value="BAMM">BAMM</option><option value="bee">bee</option><option value="beer">beer</option><option value="benjaporn poontawee">benjaporn poontawee</option><option value="benz">benz</option><option value="bird">bird</option><option value="BOBOMAN">BOBOMAN</option><option value="bot_1">bot_1</option><option value="bot_2">bot_2</option><option value="chanuporn pawamateesakul">chanuporn pawamateesakul</option><option value="Chirun">Chirun</option><option value="Ckakkrawal  Kaewsuya">Ckakkrawal  Kaewsuya</option><option value="duan">duan</option><option value="eve">eve</option><option value="faii">faii</option><option value="Fanta">Fanta</option><option value="fern">fern</option><option value="film">film</option><option value="four">four</option><option value="Hoshi 3E_battery">Hoshi 3E_battery</option><option value="im">im</option><option value="jiab">jiab</option><option value="Jittraporn(JU)">Jittraporn(JU)</option><option value="๋Jittraporn(JU)">๋Jittraporn(JU)</option><option value="joe">joe</option><option value="justin">justin</option><option value="kaew">kaew</option><option value="kanitin2">kanitin2</option><option value="kanyavee ผึ้ง">kanyavee ผึ้ง</option><option value="kee">kee</option><option value="kim">kim</option><option value="koi">koi</option><option value="Korn">Korn</option><option value="kuk">kuk</option><option value="kung">kung</option><option value="kwang">kwang</option><option value="madd">madd</option><option value="maneerat(Innovation)">maneerat(Innovation)</option><option value="martin">martin</option><option value="mick">mick</option><option value="ming">ming</option><option value="mob">mob</option><option value="mon">mon</option><option value="muk">muk</option><option value="nattinee">nattinee</option><option value="neoy">neoy</option><option value="ning">ning</option><option value="ningsale">ningsale</option><option value="NOEY">NOEY</option><option value="nuii">nuii</option><option value="nungning">nungning</option><option value="ohm">ohm</option><option value="paee">paee</option><option value="PAILIN LANONGKAN">PAILIN LANONGKAN</option><option value="Pamika (May EITA)">Pamika (May EITA)</option><option value="panadda">panadda</option><option value="Patipan">Patipan</option><option value="peemai">peemai</option><option value="Phakpapha">Phakpapha</option><option value="pimporn">pimporn</option><option value="Pirun Klangprapun">Pirun Klangprapun</option><option value="ple">ple</option><option value="ploy">ploy</option><option value="prang">prang</option><option value="pumpui">pumpui</option><option value="ResponseBy 00001">ResponseBy 00001</option><option value="ResponseBy 00010">ResponseBy 00010</option><option value="ResponseBy 00011">ResponseBy 00011</option><option value="ResponseBy 00012">ResponseBy 00012</option><option value="rungpairin">rungpairin</option><option value="Sirinapa N.">Sirinapa N.</option><option value="Sittasri(TENT EITA)">Sittasri(TENT EITA)</option><option value="som">som</option><option value="sukanay charinram">sukanay charinram</option><option value="Suprinya Yothong">Suprinya Yothong</option><option value="suthathip">suthathip</option><option value="sysadmin">sysadmin</option><option value="tae">tae</option><option value="tanaporn">tanaporn</option><option value="tanaporn นาย">tanaporn นาย</option><option value="tangmo">tangmo</option><option value="tann">tann</option><option value="tanwa">tanwa</option><option value="tee">tee</option><option value="test101">test101</option><option value="thip">thip</option><option value="tida">tida</option><option value="toomtam-sale">toomtam-sale</option><option value="Top">Top</option><option value="tug">tug</option><option value="uuyy">uuyy</option><option value="yam3E">yam3E</option><option value="Ying_EEE">Ying_EEE</option><option value="Yok">Yok</option><option value="กช">กช</option><option value="กชกรณ์ ใยรัก หมี">กชกรณ์ ใยรัก หมี</option><option value="กวาง">กวาง</option><option value="กวาง &lt;กุลธวัช&gt;">กวาง &lt;กุลธวัช&gt;</option><option value="กวาง2">กวาง2</option><option value="กัญธ์วริน แก้วใส (ปีใหม่)">กัญธ์วริน แก้วใส (ปีใหม่)</option><option value="กิจจา ศุภกิจวัฒนา">กิจจา ศุภกิจวัฒนา</option><option value="กิ๊บ">กิ๊บ</option><option value="กุ้ง">กุ้ง</option><option value="กุลสตรี มาย">กุลสตรี มาย</option><option value="แก๊ป">แก๊ป</option><option value="ขม">ขม</option><option value="ขิม">ขิม</option><option value="คณิติน">คณิติน</option><option value="คะแนน">คะแนน</option><option value="คุณฉัตร">คุณฉัตร</option><option value="จริยา">จริยา</option><option value="จามจรี ลึกงาม">จามจรี ลึกงาม</option><option value="จ๋า">จ๋า</option><option value="จิธาณ์ฒฐ์ ธีรโซติวัฒรกุล(บอย)">จิธาณ์ฒฐ์ ธีรโซติวัฒรกุล(บอย)</option><option value="จุฑามาศ">จุฑามาศ</option><option value="เจนจิรา (เอย)">เจนจิรา (เอย)</option><option value="ชัญญานุช ศรีสำราญ (นุช)">ชัญญานุช ศรีสำราญ (นุช)</option><option value="เชร์">เชร์</option><option value="ซี">ซี</option><option value="ญาณวรุตม์  รามพัด (นน)">ญาณวรุตม์  รามพัด (นน)</option><option value="ณัฎฐ์ชญาภา ทวีพิศาลชัย">ณัฎฐ์ชญาภา ทวีพิศาลชัย</option><option value="ณัฏฐ์ธมน (ออม)">ณัฏฐ์ธมน (ออม)</option><option value="แดง">แดง</option><option value="ตวงรัตน์ อ่อนเบา">ตวงรัตน์ อ่อนเบา</option><option value="ต๊ะ">ต๊ะ</option><option value="ต่าย">ต่าย</option><option value="ตาล_เช็คนอก">ตาล_เช็คนอก</option><option value="ตาลนอก">ตาลนอก</option><option value="ต้า">ต้า</option><option value="เตย (Eita)">เตย (Eita)</option><option value="แตงโม">แตงโม</option><option value="ทิพ">ทิพ</option><option value="ทิพย์">ทิพย์</option><option value="นก">นก</option><option value="นพพล ศภกิจวัฒนา">นพพล ศภกิจวัฒนา</option><option value="นภสินธุ์">นภสินธุ์</option><option value="นภา">นภา</option><option value="นรินทร์ธรณ์">นรินทร์ธรณ์</option><option value="น้อง &lt;กุลธวัช&gt;">น้อง &lt;กุลธวัช&gt;</option><option value="น้อง">น้อง</option><option value="น้อย บัญชี">น้อย บัญชี</option><option value="นัชชา บรรจงกะเสนา ณ อยุธยา">นัชชา บรรจงกะเสนา ณ อยุธยา</option><option value="นัท">นัท</option><option value="นัน">นัน</option><option value="นันทกานต์ ภัทรเจริญพงษ์">นันทกานต์ ภัทรเจริญพงษ์</option><option value="น้ำ">น้ำ</option><option value="นิธิ ดาราฉาย">นิธิ ดาราฉาย</option><option value="นุช">นุช</option><option value="นุ่น">นุ่น</option><option value="นุ้ย &lt;เอก&gt;">นุ้ย &lt;เอก&gt;</option><option value="นุ้ย">นุ้ย</option><option value="เนย">เนย</option><option value="แนน">แนน</option><option value="บิ๊ก">บิ๊ก</option><option value="บี">บี</option><option value="เบล">เบล</option><option value="เบียร์">เบียร์</option><option value="โบวี่">โบวี่</option><option value="โบว์">โบว์</option><option value="ปรรวี นาประจักษ์">ปรรวี นาประจักษ์</option><option value="ปอ">ปอ</option><option value="ปอนด์">ปอนด์</option><option value="ปาลิตา อังศุภศิริกุล (ปุ๊ก)">ปาลิตา อังศุภศิริกุล (ปุ๊ก)</option><option value="ปุ๊ก">ปุ๊ก</option><option value="ปุ้ย">ปุ้ย</option><option value="เปิ้ล">เปิ้ล</option><option value="ผักบุ้ง">ผักบุ้ง</option><option value="ผึ้ง กุลธวัช">ผึ้ง กุลธวัช</option><option value="ฝน">ฝน</option><option value="ฝ้าย">ฝ้าย</option><option value="พลอย">พลอย</option><option value="พลอย ปณิ">พลอย ปณิ</option><option value="เพชร">เพชร</option><option value="เฟรม">เฟรม</option><option value="ภู่">ภู่</option><option value="มดแดง">มดแดง</option><option value="มล">มล</option><option value="มัลลิกา (ทิพ)">มัลลิกา (ทิพ)</option><option value="มาย กุลสตรี">มาย กุลสตรี</option><option value="มิก">มิก</option><option value="มิ้น">มิ้น</option><option value="เมธาวี น้อยหนู">เมธาวี น้อยหนู</option><option value="เมย์ (กุลธวัช)">เมย์ (กุลธวัช)</option><option value="โม">โม</option><option value="แยม">แยม</option><option value="รัตนา ขำกล่ำ">รัตนา ขำกล่ำ</option><option value="ลูกกลอฟ">ลูกกลอฟ</option><option value="ลูกกอล์ฟ">ลูกกอล์ฟ</option><option value="ลูกน้ำ แก้วใส">ลูกน้ำ แก้วใส</option><option value="ลูกหมี">ลูกหมี</option><option value="ว้าล">ว้าล</option><option value="วีวี่">วีวี่</option><option value="สตางค์เอตะ(เฮียท๊อป)">สตางค์เอตะ(เฮียท๊อป)</option><option value="สตางค์เอตะ">สตางค์เอตะ</option><option value="สตางค์">สตางค์</option><option value="ส้มโอ">ส้มโอ</option><option value="สุธนัย">สุธนัย</option><option value="สุธิตา">สุธิตา</option><option value="สุภาภรณ์ อินทร์แก้ว">สุภาภรณ์ อินทร์แก้ว</option><option value="หญิง">หญิง</option><option value="หทัยชนก สันธนาคร">หทัยชนก สันธนาคร</option><option value="หทัยทิพย์">หทัยทิพย์</option><option value="หมวย">หมวย</option><option value="หมิง">หมิง</option><option value="หมี">หมี</option><option value="หมู">หมู</option><option value="หยก">หยก</option><option value="หวาน">หวาน</option><option value="ใหม่">ใหม่</option><option value="อ้อม">อ้อม</option><option value="อ้อ">อ้อ</option><option value="อัง">อัง</option><option value="อัช">อัช</option><option value="อัชชุ">อัชชุ</option><option value="เอ๊กซ์(กุลธวัช)">เอ๊กซ์(กุลธวัช)</option><option value="เอ็กซ์">เอ็กซ์</option><option value="เอกลักษณ์ นิยมจันทร์">เอกลักษณ์ นิยมจันทร์</option><option value="เอ้">เอ้</option></select>
                    <select class="form-control" name="CreateBySale"><option selected="selected" value="">Sale</option><option value="0"></option><option value="am3e">am3e</option><option value="ARTEE3E">ARTEE3E</option><option value="benjaporn poontawee">benjaporn poontawee</option><option value="Benz">Benz</option><option value="BOBOMAN">BOBOMAN</option><option value="chanuporn pawamateesakul">chanuporn pawamateesakul</option><option value="Ckakkrawal  Kaewsuya">Ckakkrawal  Kaewsuya</option><option value="FILM">FILM</option><option value="Hoshi 3E_battery">Hoshi 3E_battery</option><option value="Jiab">Jiab</option><option value="kaew">kaew</option><option value="kanitin2">kanitin2</option><option value="kung">kung</option><option value="maneerat(Innovation)">maneerat(Innovation)</option><option value="MUK">MUK</option><option value="ning">ning</option><option value="PAILIN LANONGKAN">PAILIN LANONGKAN</option><option value="Phakpapha">Phakpapha</option><option value="Pirun Klangprapun">Pirun Klangprapun</option><option value="pumpui">pumpui</option><option value="rungpairin">rungpairin</option><option value="Sirinapa N.">Sirinapa N.</option><option value="sukanay charinram">sukanay charinram</option><option value="Suprinya Yothong">Suprinya Yothong</option><option value="SYSTEM">SYSTEM</option><option value="tae">tae</option><option value="test101">test101</option><option value="toomtam-sale">toomtam-sale</option><option value="yam3E">yam3E</option><option value="Ying_EEE">Ying_EEE</option><option value="กวาง">กวาง</option><option value="กวาง &lt;กุลธวัช&gt;">กวาง &lt;กุลธวัช&gt;</option><option value="กัญธ์วริน แก้วใส (ปีใหม่)">กัญธ์วริน แก้วใส (ปีใหม่)</option><option value="กิ๊บ">กิ๊บ</option><option value="คณิติน">คณิติน</option><option value="คุณฉัตร">คุณฉัตร</option><option value="จิธาณ์ฒฐ์ ธีรโซติวัฒรกุล(บอย)">จิธาณ์ฒฐ์ ธีรโซติวัฒรกุล(บอย)</option><option value="จุฑามาศ">จุฑามาศ</option><option value="ชัญญานุช ศรีสำราญ (นุช)">ชัญญานุช ศรีสำราญ (นุช)</option><option value="เชร์">เชร์</option><option value="ญาณวรุตม์  รามพัด (นน)">ญาณวรุตม์  รามพัด (นน)</option><option value="ณัฎฐ์ชญาภา ทวีพิศาลชัย">ณัฎฐ์ชญาภา ทวีพิศาลชัย</option><option value="ตวงรัตน์ อ่อนเบา">ตวงรัตน์ อ่อนเบา</option><option value="นรินทร์ธรณ์">นรินทร์ธรณ์</option><option value="น้อง &lt;กุลธวัช&gt;">น้อง &lt;กุลธวัช&gt;</option><option value="น้อย บัญชี">น้อย บัญชี</option><option value="นัชชา บรรจงกะเสนา ณ อยุธยา">นัชชา บรรจงกะเสนา ณ อยุธยา</option><option value="นัท">นัท</option><option value="นันทกานต์ ภัทรเจริญพงษ์">นันทกานต์ ภัทรเจริญพงษ์</option><option value="นิธิ ดาราฉาย">นิธิ ดาราฉาย</option><option value="ปรรวี นาประจักษ์">ปรรวี นาประจักษ์</option><option value="ปอ">ปอ</option><option value="ปาลิตา อังศุภศิริกุล (ปุ๊ก)">ปาลิตา อังศุภศิริกุล (ปุ๊ก)</option><option value="ผักบุ้ง">ผักบุ้ง</option><option value="ผึ้ง กุลธวัช">ผึ้ง กุลธวัช</option><option value="พลอย ปณิ">พลอย ปณิ</option><option value="มล">มล</option><option value="มัลลิกา (ทิพ)">มัลลิกา (ทิพ)</option><option value="เมย์ (กุลธวัช)">เมย์ (กุลธวัช)</option><option value="รัตนา ขำกล่ำ">รัตนา ขำกล่ำ</option><option value="ลูกกอล์ฟ">ลูกกอล์ฟ</option><option value="ลูกน้ำ แก้วใส">ลูกน้ำ แก้วใส</option><option value="ลูกหมี">ลูกหมี</option><option value="สตางค์เอตะ(เฮียท๊อป)">สตางค์เอตะ(เฮียท๊อป)</option><option value="สุธนัย">สุธนัย</option><option value="สุภาภรณ์ อินทร์แก้ว">สุภาภรณ์ อินทร์แก้ว</option><option value="หทัยทิพย์">หทัยทิพย์</option><option value="หมิง">หมิง</option><option value="หมู">หมู</option><option value="อ้อม">อ้อม</option><option value="อัช">อัช</option><option value="เอ๊กซ์(กุลธวัช)">เอ๊กซ์(กุลธวัช)</option><option value="เอกลักษณ์ นิยมจันทร์">เอกลักษณ์ นิยมจันทร์</option></select>
                    <div bis_skin_checked="1"></div>

                    
                    <input class="form-control" onkeypress="submitByEnter(event)" name="SOStartDate" type="date">
                    <input class="form-control" onkeypress="submitByEnter(event)" name="SOEndDate" type="date">
                    <div bis_skin_checked="1"></div>
                </div>
            </div>

            
            <div class="so-action-row" bis_skin_checked="1">
                

                <input type="hidden" id="method" name="method" value="">
                <input type="hidden" name="perPage" value="25">
            </div>
        </form>
    </div>

                                    <div class="container mt-3" bis_skin_checked="1">
                <p class="text-center">ข้อมูลทั้งหมด 140160</p>
                <table class="table table-bordered mb-5">
                    <thead>
                        <tr class="table-success">
                            <th scope="col">SO</th>
                            <th scope="col">รหัสลูกค้า</th>
                            <th scope="col">ชื่อลูกค้า</th>
                            <th scope="col">สถานะ</th>
                            <th scope="col">วันส่ง</th>
                        </tr>
                    </thead>
                    <tbody>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;">
                                    <a href="http://server_update:8000/sodetail?SONum=69/014203" style="color:black;">69/014203</a>
                                </td>
                                <td>CUS-06003</td>
                                <td>แคล-คอมพ์ อีเล็คโทรนิคส์ (ประเทศไทย) จำกัด (มหาชน)      </td>
                                <td>PARTIAL</td>
                                <td style="background-color:white">2026-07-15</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;">
                                    <a href="http://server_update:8000/sodetail?SONum=69/014202" style="color:black;">69/014202</a>
                                </td>
                                <td>CUS-11315</td>
                                <td>เคซีอี เทคโนโลยี จำกัด</td>
                                <td>PARTIAL</td>
                                <td style="background-color:white">2026-08-08</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;">
                                    <a href="http://server_update:8000/sodetail?SONum=69/014201" style="color:black;">69/014201</a>
                                </td>
                                <td>CUS-07257.2</td>
                                <td>สยามคูโบต้าคอร์ปอเรชั่น จำกัด</td>
                                <td>PARTIAL</td>
                                <td style="background-color:white">2026-07-20</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;">
                                    <a href="http://server_update:8000/sodetail?SONum=69/014199" style="color:black;">69/014199</a>
                                </td>
                                <td>CUS-06111.6</td>
                                <td>นิปปอน สตีล ไพพ์ (ประเทศไทย) จำกัด</td>
                                <td>PARTIAL</td>
                                <td style="background-color:white">2026-07-15</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:white;">
                                    <a href="http://server_update:8000/sodetail?SONum=69/014198" style="color:black;">69/014198</a>
                                </td>
                                <td>CUS-12837.2</td>
                                <td>ATS DIVISION CO., LTD.</td>
                                <td>PARTIAL</td>
                                <td style="background-color:white">2026-08-03</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;">
                                    <a href="http://server_update:8000/sodetail?SONum=69/014195" style="color:black;">69/014195</a>
                                </td>
                                <td>CUS-09108.7</td>
                                <td>ปูนซีเมนต์เอเชีย จำกัด (มหาชน)</td>
                                <td>PARTIAL</td>
                                <td style="background-color:white">2026-10-30</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;">
                                    <a href="http://server_update:8000/sodetail?SONum=69/014191" style="color:black;">69/014191</a>
                                </td>
                                <td>CUS-09108.7</td>
                                <td>ปูนซีเมนต์เอเชีย จำกัด (มหาชน)</td>
                                <td>PARTIAL</td>
                                <td style="background-color:white">2026-10-31</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;">
                                    <a href="http://server_update:8000/sodetail?SONum=69/014187" style="color:black;">69/014187</a>
                                </td>
                                <td>CUS-09195</td>
                                <td>HMC Polymers Company Limited</td>
                                <td>PARTIAL</td>
                                <td style="background-color:white">2026-07-31</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;">
                                    <a href="http://server_update:8000/sodetail?SONum=69/014184" style="color:black;">69/014184</a>
                                </td>
                                <td>CUS-07573.1</td>
                                <td>แอคเซพ เทค จำกัด</td>
                                <td>PARTIAL</td>
                                <td style="background-color:white">2026-07-17</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;">
                                    <a href="http://server_update:8000/sodetail?SONum=69/014181" style="color:black;">69/014181</a>
                                </td>
                                <td>CUS-07352.1</td>
                                <td>เพาเวอร์ เทค อีเลคทริค จำกัด</td>
                                <td>PARTIAL</td>
                                <td style="background-color:white">2026-07-15</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;">
                                    <a href="http://server_update:8000/sodetail?SONum=69/014180" style="color:black;">69/014180</a>
                                </td>
                                <td>CUS-19603</td>
                                <td>ปาร์ค สวนพลู โฮลดิ้งส์ จำกัด</td>
                                <td>PARTIAL</td>
                                <td style="background-color:white">2026-07-15</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;">
                                    <a href="http://server_update:8000/sodetail?SONum=69/014177" style="color:black;">69/014177</a>
                                </td>
                                <td>CUS-10016.1</td>
                                <td>เหล็กสยามยามาโตะ จำกัด</td>
                                <td>PARTIAL</td>
                                <td style="background-color:white">2026-08-10</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;">
                                    <a href="http://server_update:8000/sodetail?SONum=69/014175" style="color:black;">69/014175</a>
                                </td>
                                <td>CUS-10016.2</td>
                                <td>เหล็กสยามยามาโตะ จำกัด</td>
                                <td>PARTIAL</td>
                                <td style="background-color:white">2026-09-10</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;">
                                    <a href="http://server_update:8000/sodetail?SONum=69/014174" style="color:black;">69/014174</a>
                                </td>
                                <td>CUS-14942.1</td>
                                <td>จิราพัชร อิเล็คทริค จำกัด</td>
                                <td>PARTIAL</td>
                                <td style="background-color:white">2026-07-11</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;">
                                    <a href="http://server_update:8000/sodetail?SONum=69/014172" style="color:black;">69/014172</a>
                                </td>
                                <td>CUS-06621.2</td>
                                <td>อีไอซี เซมิคอนดักเตอร์ จำกัด</td>
                                <td>PARTIAL</td>
                                <td style="background-color:white">2026-07-16</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;">
                                    <a href="http://server_update:8000/sodetail?SONum=69/014171" style="color:black;">69/014171</a>
                                </td>
                                <td>CUS-06419.2</td>
                                <td>ซีเอฟที เอ็นจิเนียริ่ง จำกัด</td>
                                <td>PARTIAL</td>
                                <td style="background-color:white">2026-07-19</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;">
                                    <a href="http://server_update:8000/sodetail?SONum=69/014170" style="color:black;">69/014170</a>
                                </td>
                                <td>CUS-09195</td>
                                <td>HMC Polymers Company Limited</td>
                                <td>PARTIAL</td>
                                <td style="background-color:white">2026-07-31</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;">
                                    <a href="http://server_update:8000/sodetail?SONum=69/014169" style="color:black;">69/014169</a>
                                </td>
                                <td>CUS-10016.1</td>
                                <td>เหล็กสยามยามาโตะ จำกัด</td>
                                <td>PARTIAL</td>
                                <td style="background-color:white">2026-09-10</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;">
                                    <a href="http://server_update:8000/sodetail?SONum=69/014167" style="color:black;">69/014167</a>
                                </td>
                                <td>CUS-06226.7</td>
                                <td>แอร์โรเฟลกซ์ จำกัด</td>
                                <td>PARTIAL</td>
                                <td style="background-color:white">2026-07-16</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;">
                                    <a href="http://server_update:8000/sodetail?SONum=69/014165" style="color:black;">69/014165</a>
                                </td>
                                <td>CUS-08668</td>
                                <td>Ford Motor Company (Thailand) Ltd.</td>
                                <td>PARTIAL</td>
                                <td style="background-color:white">2026-07-14</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;">
                                    <a href="http://server_update:8000/sodetail?SONum=69/014159" style="color:black;">69/014159</a>
                                </td>
                                <td>CUS-07573.1</td>
                                <td>แอคเซพ เทค จำกัด</td>
                                <td>PARTIAL</td>
                                <td style="background-color:white">2026-07-17</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;">
                                    <a href="http://server_update:8000/sodetail?SONum=69/014158" style="color:black;">69/014158</a>
                                </td>
                                <td>CUS-06111.6</td>
                                <td>นิปปอน สตีล ไพพ์ (ประเทศไทย) จำกัด</td>
                                <td>PARTIAL</td>
                                <td style="background-color:white">2026-07-19</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;">
                                    <a href="http://server_update:8000/sodetail?SONum=69/014156" style="color:black;">69/014156</a>
                                </td>
                                <td>CUS-08802</td>
                                <td>สามชัยสตีล อินดัสทรี จำกัด (มหาชน)</td>
                                <td>PARTIAL</td>
                                <td style="background-color:white">2026-07-16</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;">
                                    <a href="http://server_update:8000/sodetail?SONum=69/014155" style="color:black;">69/014155</a>
                                </td>
                                <td>CUS-06020</td>
                                <td>เอเอสเอ  บ๊อกซ์บอร์ด  คอนเทนเนอร์  จำกัด</td>
                                <td>PARTIAL</td>
                                <td style="background-color:white">2026-07-23</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;">
                                    <a href="http://server_update:8000/sodetail?SONum=69/014154" style="color:black;">69/014154</a>
                                </td>
                                <td>CUS-19615</td>
                                <td>เอตะ แอนด์ พอล อินโนเวชั่น จำกัด </td>
                                <td>PARTIAL</td>
                                <td style="background-color:white">2026-08-09</td>
                            </tr>
                                            </tbody>
                </table>
                <div class="d-flex justify-content-center" bis_skin_checked="1">
                    <nav>
        <ul class="pagination">
            
                            <li class="page-item disabled" aria-disabled="true" aria-label="« Previous">
                    <span class="page-link" aria-hidden="true">‹</span>
                </li>
            
            
                            
                
                
                                                                                        <li class="page-item active" aria-current="page"><span class="page-link">1</span></li>
                                                                                                <li class="page-item"><a class="page-link" href="http://server_update:8000/solist?_token=r6HllrlTWyA6Gikm7c5g1qQt2JZJjgThuZvSRkCZ&amp;method=search&amp;perPage=25&amp;page=2">2</a></li>
                                                                                                <li class="page-item"><a class="page-link" href="http://server_update:8000/solist?_token=r6HllrlTWyA6Gikm7c5g1qQt2JZJjgThuZvSRkCZ&amp;method=search&amp;perPage=25&amp;page=3">3</a></li>
                                                                                                <li class="page-item"><a class="page-link" href="http://server_update:8000/solist?_token=r6HllrlTWyA6Gikm7c5g1qQt2JZJjgThuZvSRkCZ&amp;method=search&amp;perPage=25&amp;page=4">4</a></li>
                                                                                                <li class="page-item"><a class="page-link" href="http://server_update:8000/solist?_token=r6HllrlTWyA6Gikm7c5g1qQt2JZJjgThuZvSRkCZ&amp;method=search&amp;perPage=25&amp;page=5">5</a></li>
                                                                                                <li class="page-item"><a class="page-link" href="http://server_update:8000/solist?_token=r6HllrlTWyA6Gikm7c5g1qQt2JZJjgThuZvSRkCZ&amp;method=search&amp;perPage=25&amp;page=6">6</a></li>
                                                                                                <li class="page-item"><a class="page-link" href="http://server_update:8000/solist?_token=r6HllrlTWyA6Gikm7c5g1qQt2JZJjgThuZvSRkCZ&amp;method=search&amp;perPage=25&amp;page=7">7</a></li>
                                                                                                <li class="page-item"><a class="page-link" href="http://server_update:8000/solist?_token=r6HllrlTWyA6Gikm7c5g1qQt2JZJjgThuZvSRkCZ&amp;method=search&amp;perPage=25&amp;page=8">8</a></li>
                                                                                                <li class="page-item"><a class="page-link" href="http://server_update:8000/solist?_token=r6HllrlTWyA6Gikm7c5g1qQt2JZJjgThuZvSRkCZ&amp;method=search&amp;perPage=25&amp;page=9">9</a></li>
                                                                                                <li class="page-item"><a class="page-link" href="http://server_update:8000/solist?_token=r6HllrlTWyA6Gikm7c5g1qQt2JZJjgThuZvSRkCZ&amp;method=search&amp;perPage=25&amp;page=10">10</a></li>
                                                                                        
                                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">...</span></li>
                
                
                                            
                
                
                                                                                        <li class="page-item"><a class="page-link" href="http://server_update:8000/solist?_token=r6HllrlTWyA6Gikm7c5g1qQt2JZJjgThuZvSRkCZ&amp;method=search&amp;perPage=25&amp;page=5606">5606</a></li>
                                                                                                <li class="page-item"><a class="page-link" href="http://server_update:8000/solist?_token=r6HllrlTWyA6Gikm7c5g1qQt2JZJjgThuZvSRkCZ&amp;method=search&amp;perPage=25&amp;page=5607">5607</a></li>
                                                                        
            
                            <li class="page-item">
                    <a class="page-link" href="http://server_update:8000/solist?_token=r6HllrlTWyA6Gikm7c5g1qQt2JZJjgThuZvSRkCZ&amp;method=search&amp;perPage=25&amp;page=2" rel="next" aria-label="Next »">›</a>
                </li>
                    </ul>
    </nav>

                </div>
            </div>
                    </main>
    </div>

    <script>
        function submitByEnter(e) {
            if (!e) e = window.event;
            var code;
            if ((e.charCode) && (e.keyCode == 0)) {
                code = e.charCode
            } else {
                code = e.keyCode;
            }
            if (code == 13) {
                submitForm("search");
            }
        }

        function submitForm(_action) {
            if (_action == 'search') {
                document.getElementById('method').value = _action
                document.getElementById('searchForm').submit();
            }
        }

        function resetCriteria() {
            let formElements = document.getElementById('searchForm');
            for (let i = 0; i < formElements.length; i++) {
                if (formElements[i].type == 'text') {
                    formElements[i].value = null
                } else if (formElements[i].type == 'checkbox') {
                    formElements[i].checked = false
                } else if (formElements[i].type == 'select-one') {
                    formElements[i].selectedIndex = 0;
                } else if (formElements[i].type == 'date') {
                    formElements[i].value = null
                }
            }
        }

        function initDropdown(object, selectValue) {
            for (i = 0; i < object.options.length; i++) {
                if (object.item(i).value == selectValue) {
                    object.selectedIndex = i;
                }
            }
        }
        initDropdown(document.getElementById('SOStatus'), "");
    </script>


</body></html>