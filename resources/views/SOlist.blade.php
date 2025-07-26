<html lang="en"><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="ex7i8gpx2CqIgN6D6MtCQoNU2qBJMv0amRWw6mOg">

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
                SONums.forEach((_SONum)=>{
                    console.log('td'+_SONum.SONum)
                    document.getElementById('td'+_SONum.SONum).innerHTML = _SONum.DocuStatus
                })
            }
        }
    </script>
</head>

<body>
    <div id="app"><nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm"><a href="javascript:history.back()"><img src="http://server_update:8000/images/left-arrow.png" style="width: 30px; height: 30px;"></a> <div class="container"><a href="http://server_update:8000" class="navbar-brand"><img src="http://server_update:8000/images/LOGO_3E.jpg" style="width: 50px; height: 50px;"></a> <ul id="navHeaderMenu"><li><a href="javascript:void(0)"><button type="button" class="btn btn-secondary dropdown-toggle" style="width: 100%; background: rgb(233, 105, 75);">MENU</button></a> <ul><li><a href="http://server_update:8000/solist">SO</a></li><li><a href="/disbursement">เบิกจ่าย</a></li><li><a href="/bill_requirement">ใบ Requirement</a></li></ul></li></ul> <button type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler"><span class="navbar-toggler-icon"></span></button> <div id="navbarSupportedContent" class="collapse navbar-collapse"><ul class="navbar-nav mr-auto"></ul> <ul class="navbar-nav ml-auto"><li class="nav-item"><a href="http://server_update:8000/login" class="nav-link">Login</a></li> <li class="nav-item"><a href="http://server_update:8000/register" class="nav-link">Register</a></li></ul></div></div></nav></div>
    <div class="container-fluid">
        <main class="py-3">
            
    <div class="container" style="width: 900px;">

        <form action="http://server_update:8000/solist" method="GET" id="searchForm">
            <div class="row">
                <div class="col-md-12">
                    <input type="hidden" name="_token" value="ex7i8gpx2CqIgN6D6MtCQoNU2qBJMv0amRWw6mOg">                    <div style="border: 2px solid rgb(150, 150, 150);border-radius: 8px;padding: 2%;">
                        <table>
                            <tbody>
                                <tr>
                                    <td style="width:50px;">
                                        <div>SO</div>
                                    </td>
                                    <td style="width:150px;">
                                        <input class="form-control" onkeypress="submitByEnter(event)" name="SONum" type="text">
                                    </td>
                                    <td>
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td style="width:80px;">
                                                        <div>รหัสลูกค้า</div>
                                                    </td>
                                                    <td style="width:150px;">
                                                        <input class="form-control" onkeypress="submitByEnter(event)" name="CustomerCode" type="text">
                                                    </td>
                                                    <td>
                                                        <input class="form-control" onkeypress="submitByEnter(event)" name="CustomerName" type="text">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div>PO</div>
                                    </td>
                                    <td style="width:150px;"><input class="form-control" onkeypress="submitByEnter(event)" name="PONum" type="text">
                                    </td>
                                    <td>
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td style="width:80px;">
                                                        <div>สถานะ&nbsp;&nbsp;</div>
                                                    </td>
                                                    <td>
                                                        

                                                        <select name="SOStatus" id="SOStatus" size="1" class="form-control">
                                                            <option value=""></option>
                                                            <option value="SO_ENTRY" style="background-color:#FF99FF;">เปิด
                                                                PO
                                                                ไม่ครบ</option>
                                                            <option value="RECEIPT_PARTIAL" style="background-color:yellow;">
                                                                ของมาบางส่วน</option>
                                                            <option value="RECEIPT_ALL" style="background-color:green;">
                                                                ของมาครบ
                                                            </option>
                                                            <option value="SO_COMPLETED" style="background-color:white;">
                                                                สมบูรณ์
                                                            </option>
                                                            <option value="SO_NO_COMPLETE" style="background-color:rgb(224, 77, 77);">
                                                                ไม่ COMPLETE
                                                            </option>
                                                        </select>
                                                    </td>
                                                    <td style="text-align:right;"><input name="delaySO" type="checkbox" value="Y"></td>
                                                    <td>
                                                        SO เลยกำหนด
                                                    </td>
                                                    <td style="width:260px;"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:50px;">
                                        <div>Bill</div>
                                    </td>
                                    <td style="width:150px;">
                                        <input class="form-control" onkeypress="submitByEnter(event)" name="BillNum" type="text">
                                        
                                    </td>
                                    <td>
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td style="width:80px;">
                                                        <div>PO อ้างอิง&nbsp;&nbsp;</div>
                                                    </td>
                                                    <td style="width:150px;">
                                                        <input class="form-control" onkeypress="submitByEnter(event)" name="CustPONo" type="text">
                                                        
                                                    </td>
                                                    <td style="width:80px;">
                                                        <div>ผู้รับผิดชอบ</div>
                                                    </td>
                                                    <td style="width:100px;">
                                                        <select class="form-control" name="ResponseBy"><option value="0"></option><option value="" selected="selected"></option><option value="am3e">am3e</option><option value="Aor">Aor</option><option value="ARTEE3E">ARTEE3E</option><option value="Atitaya">Atitaya</option><option value="bee">bee</option><option value="beer">beer</option><option value="benjaporn poontawee">benjaporn poontawee</option><option value="benz">benz</option><option value="bird">bird</option><option value="BOBOMAN">BOBOMAN</option><option value="bot_1">bot_1</option><option value="chanuporn pawamateesakul">chanuporn pawamateesakul</option><option value="Chirun">Chirun</option><option value="Ckakkrawal  Kaewsuya">Ckakkrawal  Kaewsuya</option><option value="duan">duan</option><option value="eve">eve</option><option value="faii">faii</option><option value="Fanta">Fanta</option><option value="fern">fern</option><option value="film">film</option><option value="four">four</option><option value="Hoshi 3E_battery">Hoshi 3E_battery</option><option value="im">im</option><option value="jiab">jiab</option><option value="Jittraporn(JU)">Jittraporn(JU)</option><option value="๋Jittraporn(JU)">๋Jittraporn(JU)</option><option value="joe">joe</option><option value="justin">justin</option><option value="kaew">kaew</option><option value="kanitin2">kanitin2</option><option value="kanyavee ผึ้ง">kanyavee ผึ้ง</option><option value="kee">kee</option><option value="kim">kim</option><option value="koi">koi</option><option value="Korn">Korn</option><option value="kuk">kuk</option><option value="kung">kung</option><option value="kwang">kwang</option><option value="madd">madd</option><option value="maneerat(Innovation)">maneerat(Innovation)</option><option value="martin">martin</option><option value="mick">mick</option><option value="ming">ming</option><option value="mob">mob</option><option value="mon">mon</option><option value="muk">muk</option><option value="nattinee">nattinee</option><option value="neoy">neoy</option><option value="ning">ning</option><option value="ningsale">ningsale</option><option value="NOEY">NOEY</option><option value="nuii">nuii</option><option value="nungning">nungning</option><option value="ohm">ohm</option><option value="paee">paee</option><option value="PAILIN LANONGKAN">PAILIN LANONGKAN</option><option value="Pamika (May EITA)">Pamika (May EITA)</option><option value="panadda">panadda</option><option value="Patipan">Patipan</option><option value="peemai">peemai</option><option value="Phakpapha">Phakpapha</option><option value="pimporn">pimporn</option><option value="Pirun Klangprapun">Pirun Klangprapun</option><option value="ple">ple</option><option value="ploy">ploy</option><option value="prang">prang</option><option value="pumpui">pumpui</option><option value="ResponseBy 00001">ResponseBy 00001</option><option value="ResponseBy 00010">ResponseBy 00010</option><option value="ResponseBy 00011">ResponseBy 00011</option><option value="ResponseBy 00012">ResponseBy 00012</option><option value="rungpairin">rungpairin</option><option value="Sirinapa N.">Sirinapa N.</option><option value="Sittasri(TENT EITA)">Sittasri(TENT EITA)</option><option value="som">som</option><option value="sukanay charinram">sukanay charinram</option><option value="Suprinya Yothong">Suprinya Yothong</option><option value="suthathip">suthathip</option><option value="sysadmin">sysadmin</option><option value="tae">tae</option><option value="tanaporn">tanaporn</option><option value="tanaporn นาย">tanaporn นาย</option><option value="tangmo">tangmo</option><option value="tann">tann</option><option value="tanwa">tanwa</option><option value="tee">tee</option><option value="thip">thip</option><option value="tida">tida</option><option value="toomtam-sale">toomtam-sale</option><option value="Top">Top</option><option value="tug">tug</option><option value="uuyy">uuyy</option><option value="yam3E">yam3E</option><option value="Ying_EEE">Ying_EEE</option><option value="Yok">Yok</option><option value="กช">กช</option><option value="กชกรณ์ ใยรัก หมี">กชกรณ์ ใยรัก หมี</option><option value="กวาง">กวาง</option><option value="กวาง <กุลธวัช>">กวาง &lt;กุลธวัช&gt;</option><option value="กวาง2">กวาง2</option><option value="กิจจา ศุภกิจวัฒนา">กิจจา ศุภกิจวัฒนา</option><option value="กิ๊บ">กิ๊บ</option><option value="กุ้ง">กุ้ง</option><option value="แก๊ป">แก๊ป</option><option value="ขม">ขม</option><option value="ขิม">ขิม</option><option value="คณิติน">คณิติน</option><option value="คะแนน">คะแนน</option><option value="คุณฉัตร">คุณฉัตร</option><option value="จริยา">จริยา</option><option value="จามจรี ลึกงาม">จามจรี ลึกงาม</option><option value="จ๋า">จ๋า</option><option value="จิธาณ์ฒฐ์ ธีรโซติวัฒรกุล(บอย)">จิธาณ์ฒฐ์ ธีรโซติวัฒรกุล(บอย)</option><option value="จุฑามาศ">จุฑามาศ</option><option value="เจนจิรา (เอย)">เจนจิรา (เอย)</option><option value="ชัญญานุช ศรีสำราญ (นุช)">ชัญญานุช ศรีสำราญ (นุช)</option><option value="เชร์">เชร์</option><option value="ซี">ซี</option><option value="ญาณวรุตม์  รามพัด (นน)">ญาณวรุตม์  รามพัด (นน)</option><option value="ณัฎฐ์ชญาภา ทวีพิศาลชัย">ณัฎฐ์ชญาภา ทวีพิศาลชัย</option><option value="ณัฏฐ์ธมน (ออม)">ณัฏฐ์ธมน (ออม)</option><option value="แดง">แดง</option><option value="ตวงรัตน์ อ่อนเบา">ตวงรัตน์ อ่อนเบา</option><option value="ต๊ะ">ต๊ะ</option><option value="ต่าย">ต่าย</option><option value="ตาล_เช็คนอก">ตาล_เช็คนอก</option><option value="ตาลนอก">ตาลนอก</option><option value="ต้า">ต้า</option><option value="เตย (Eita)">เตย (Eita)</option><option value="แตงโม">แตงโม</option><option value="ทิพ">ทิพ</option><option value="ทิพย์">ทิพย์</option><option value="นก">นก</option><option value="นพพล ศภกิจวัฒนา">นพพล ศภกิจวัฒนา</option><option value="นภสินธุ์">นภสินธุ์</option><option value="นภา">นภา</option><option value="นรินทร์ธรณ์">นรินทร์ธรณ์</option><option value="น้อง <กุลธวัช>">น้อง &lt;กุลธวัช&gt;</option><option value="น้อง">น้อง</option><option value="น้อย บัญชี">น้อย บัญชี</option><option value="นัชชา บรรจงกะเสนา ณ อยุธยา">นัชชา บรรจงกะเสนา ณ อยุธยา</option><option value="นัท">นัท</option><option value="นัน">นัน</option><option value="นันทกานต์ ภัทรเจริญพงษ์">นันทกานต์ ภัทรเจริญพงษ์</option><option value="น้ำ">น้ำ</option><option value="นิธิ ดาราฉาย">นิธิ ดาราฉาย</option><option value="นุช">นุช</option><option value="นุ่น">นุ่น</option><option value="นุ้ย <เอก>">นุ้ย &lt;เอก&gt;</option><option value="นุ้ย">นุ้ย</option><option value="เนย">เนย</option><option value="แนน">แนน</option><option value="บิ๊ก">บิ๊ก</option><option value="บี">บี</option><option value="เบล">เบล</option><option value="โบวี่">โบวี่</option><option value="โบว์">โบว์</option><option value="ปรรวี นาประจักษ์">ปรรวี นาประจักษ์</option><option value="ปอ">ปอ</option><option value="ปอนด์">ปอนด์</option><option value="ปาลิตา อังศุภศิริกุล (ปุ๊ก)">ปาลิตา อังศุภศิริกุล (ปุ๊ก)</option><option value="ปุ๊ก">ปุ๊ก</option><option value="ปุ้ย">ปุ้ย</option><option value="เปิ้ล">เปิ้ล</option><option value="ผักบุ้ง">ผักบุ้ง</option><option value="ผึ้ง กุลธวัช">ผึ้ง กุลธวัช</option><option value="ฝน">ฝน</option><option value="ฝ้าย">ฝ้าย</option><option value="พลอย">พลอย</option><option value="พลอย ปณิ">พลอย ปณิ</option><option value="เพชร">เพชร</option><option value="เฟรม">เฟรม</option><option value="ภู่">ภู่</option><option value="มดแดง">มดแดง</option><option value="มล">มล</option><option value="มิก">มิก</option><option value="มิ้น">มิ้น</option><option value="เมธาวี น้อยหนู">เมธาวี น้อยหนู</option><option value="เมย์ (กุลธวัช)">เมย์ (กุลธวัช)</option><option value="โม">โม</option><option value="แยม">แยม</option><option value="ลูกกลอฟ">ลูกกลอฟ</option><option value="ลูกกอล์ฟ">ลูกกอล์ฟ</option><option value="ลูกน้ำ แก้วใส">ลูกน้ำ แก้วใส</option><option value="ลูกหมี">ลูกหมี</option><option value="ว้าล">ว้าล</option><option value="วีวี่">วีวี่</option><option value="สตางค์เอตะ(เฮียท๊อป)">สตางค์เอตะ(เฮียท๊อป)</option><option value="สตางค์เอตะ">สตางค์เอตะ</option><option value="สตางค์">สตางค์</option><option value="ส้มโอ">ส้มโอ</option><option value="สุธนัย">สุธนัย</option><option value="สุธิตา">สุธิตา</option><option value="สุภาภรณ์ อินทร์แก้ว">สุภาภรณ์ อินทร์แก้ว</option><option value="หญิง">หญิง</option><option value="หทัยชนก สันธนาคร">หทัยชนก สันธนาคร</option><option value="หทัยทิพย์">หทัยทิพย์</option><option value="หมวย">หมวย</option><option value="หมิง">หมิง</option><option value="หมี">หมี</option><option value="หมู">หมู</option><option value="หยก">หยก</option><option value="หวาน">หวาน</option><option value="ใหม่">ใหม่</option><option value="อ้อม">อ้อม</option><option value="อ้อ">อ้อ</option><option value="อัง">อัง</option><option value="อัช">อัช</option><option value="อัชชุ">อัชชุ</option><option value="เอ๊กซ์(กุลธวัช)">เอ๊กซ์(กุลธวัช)</option><option value="เอ็กซ์">เอ็กซ์</option><option value="เอกลักษณ์ นิยมจันทร์">เอกลักษณ์ นิยมจันทร์</option><option value="เอ้">เอ้</option></select>
                                                    </td>
                                                    <td style="width:30px;">
                                                        <div>&nbsp;Sale</div>
                                                    </td>
                                                    <td style="width:150px;">
                                                        <select class="form-control" name="CreateBySale"><option value="0" selected="selected"></option><option value="am3e">am3e</option><option value="ARTEE3E">ARTEE3E</option><option value="benjaporn poontawee">benjaporn poontawee</option><option value="Benz">Benz</option><option value="BOBOMAN">BOBOMAN</option><option value="chanuporn pawamateesakul">chanuporn pawamateesakul</option><option value="FILM">FILM</option><option value="Hoshi 3E_battery">Hoshi 3E_battery</option><option value="Jiab">Jiab</option><option value="kanitin2">kanitin2</option><option value="kung">kung</option><option value="maneerat(Innovation)">maneerat(Innovation)</option><option value="MUK">MUK</option><option value="ning">ning</option><option value="PAILIN LANONGKAN">PAILIN LANONGKAN</option><option value="Phakpapha">Phakpapha</option><option value="Pirun Klangprapun">Pirun Klangprapun</option><option value="pumpui">pumpui</option><option value="rungpairin">rungpairin</option><option value="Sirinapa N.">Sirinapa N.</option><option value="sukanay charinram">sukanay charinram</option><option value="Suprinya Yothong">Suprinya Yothong</option><option value="SYSTEM">SYSTEM</option><option value="tae">tae</option><option value="toomtam-sale">toomtam-sale</option><option value="yam3E">yam3E</option><option value="Ying_EEE">Ying_EEE</option><option value="กวาง">กวาง</option><option value="กวาง <กุลธวัช>">กวาง &lt;กุลธวัช&gt;</option><option value="กิ๊บ">กิ๊บ</option><option value="คณิติน">คณิติน</option><option value="คุณฉัตร">คุณฉัตร</option><option value="จิธาณ์ฒฐ์ ธีรโซติวัฒรกุล(บอย)">จิธาณ์ฒฐ์ ธีรโซติวัฒรกุล(บอย)</option><option value="จุฑามาศ">จุฑามาศ</option><option value="ชัญญานุช ศรีสำราญ (นุช)">ชัญญานุช ศรีสำราญ (นุช)</option><option value="เชร์">เชร์</option><option value="ญาณวรุตม์  รามพัด (นน)">ญาณวรุตม์  รามพัด (นน)</option><option value="ณัฎฐ์ชญาภา ทวีพิศาลชัย">ณัฎฐ์ชญาภา ทวีพิศาลชัย</option><option value="ตวงรัตน์ อ่อนเบา">ตวงรัตน์ อ่อนเบา</option><option value="นรินทร์ธรณ์">นรินทร์ธรณ์</option><option value="น้อง <กุลธวัช>">น้อง &lt;กุลธวัช&gt;</option><option value="น้อย บัญชี">น้อย บัญชี</option><option value="นัชชา บรรจงกะเสนา ณ อยุธยา">นัชชา บรรจงกะเสนา ณ อยุธยา</option><option value="นัท">นัท</option><option value="นันทกานต์ ภัทรเจริญพงษ์">นันทกานต์ ภัทรเจริญพงษ์</option><option value="นิธิ ดาราฉาย">นิธิ ดาราฉาย</option><option value="ปรรวี นาประจักษ์">ปรรวี นาประจักษ์</option><option value="ปอ">ปอ</option><option value="ปาลิตา อังศุภศิริกุล (ปุ๊ก)">ปาลิตา อังศุภศิริกุล (ปุ๊ก)</option><option value="ผึ้ง กุลธวัช">ผึ้ง กุลธวัช</option><option value="พลอย ปณิ">พลอย ปณิ</option><option value="มล">มล</option><option value="เมย์ (กุลธวัช)">เมย์ (กุลธวัช)</option><option value="ลูกกอล์ฟ">ลูกกอล์ฟ</option><option value="ลูกน้ำ แก้วใส">ลูกน้ำ แก้วใส</option><option value="ลูกหมี">ลูกหมี</option><option value="สตางค์เอตะ(เฮียท๊อป)">สตางค์เอตะ(เฮียท๊อป)</option><option value="สุธนัย">สุธนัย</option><option value="สุภาภรณ์ อินทร์แก้ว">สุภาภรณ์ อินทร์แก้ว</option><option value="หทัยทิพย์">หทัยทิพย์</option><option value="หมิง">หมิง</option><option value="หมู">หมู</option><option value="อ้อม">อ้อม</option><option value="อัช">อัช</option><option value="เอ๊กซ์(กุลธวัช)">เอ๊กซ์(กุลธวัช)</option><option value="เอกลักษณ์ นิยมจันทร์">เอกลักษณ์ นิยมจันทร์</option></select>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:50px;">วันที่</td>
                                    <td style="width:150px;"><input class="form-control" onkeypress="submitByEnter(event)" name="SOStartDate" type="date">
                                    </td>
                                    <td>
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td style="width:80px;">
                                                        <div>ถึง</div>
                                                    </td>
                                                    <td style="width:150px;">
                                                        <input class="form-control" onkeypress="submitByEnter(event)" name="SOEndDate" type="date">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <input class="btn btn-primary" type="button" name="search" value="search" onclick="submitForm(this.name)">
                    <input class="btn btn-danger" type="button" name="reset" value="reset" onclick="resetCriteria()">
                    <a href="http://server_update:8000/create_so"><input class="btn btn-secondary" type="button" name="action" value="สร้าง SO"></a>
                    <input type="hidden" id="method" name="method" value="">
                    {{-- ไปเพิ่มเซิฟหลัก --}}
                   <a id="btn-dashboard">
    <input class="btn btn-danger" type="button" style="background-color: #C599B6" value="ข้อมูลจัดส่ง">
</a>
<a id="btn-dashboardpo">
    <input class="btn btn-danger" type="button" style="background-color: #80CBC4" value="ข้อมูลรับของ PO">
</a>
<a id="btn-dashboarddoc">
    <input class="btn btn-danger" type="button" style="background-color: #E9762B" value="เอกสารชั่วคราว">
</a>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // ดึงวันที่ปัจจุบันในรูปแบบ YYYY-MM-DD
        const today = new Date().toISOString().split('T')[0];

        // เพิ่มพารามิเตอร์วันที่เข้าไปในลิงก์
        document.getElementById("btn-dashboard").href = `dashboard?date=${today}`;
        document.getElementById("btn-dashboardpo").href = `dashboardpo?date=${today}`;
        document.getElementById("btn-dashboarddoc").href = `dashboarddoc?date=${today}`;
    });
</script>
                 <a href="alertaccount" title="บัญชี" class="notification-icon" style="position: relative; display: inline-block;">
                <input class="btn btn-danger" type="button" style="background-color: pink" value="บัญชี">
                
                    {{-- ไปเพิ่มเซิฟหลัก --}}
                <span class="notification-badge" id="alertAccountBadge" style="position: absolute; top: -5px; right: -5px; background-color: rgb(255, 0, 0); color: white; border-radius: 50%; width: 20px; height: 20px; display: none; text-align: center; line-height: 20px;">0</span>
            </a>
            
                    <input type="hidden" name="perPage" value="25">
                </div>
            </div>
        </form>
        
    </div>
                                    <div class="container mt-1">
                <p class="text-center">ข้อมูลทั้งหมด 145668</p>
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
                                <td scope="row" style="background-color:white;"><a href="
                                    http://server_update:8000/sodetail?SONum=68/004921" style="color:black;">
                                    68/004921</a>
                                </td>
                                <td>CUS-06226</td>
                                <td>อีสเทิร์น  โพลีแพค  จำกัด</td>

                                <td id="tdSO68/004921">PARTIAL</td>
                                <td style="background-color:white">2025-03-12</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;"><a href="
                                    http://server_update:8000/sodetail?SONum=68/004920" style="color:black;">
                                    68/004920</a>
                                </td>
                                <td>CUS-10101</td>
                                <td>THAI-GERMAN SPECIALTY GLASS CO.,LTD.</td>

                                <td id="tdSO68/004920">PARTIAL</td>
                                <td style="background-color:white">2025-03-16</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;"><a href="
                                    http://server_update:8000/sodetail?SONum=68/004917" style="color:black;">
                                    68/004917</a>
                                </td>
                                <td>CUS-10817</td>
                                <td>วนวิทย์ แมนูแฟคเจอริ่ง จำกัด</td>

                                <td id="tdSO68/004917">PARTIAL</td>
                                <td style="background-color:white">2025-03-17</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;"><a href="
                                    http://server_update:8000/sodetail?SONum=68/004916" style="color:black;">
                                    68/004916</a>
                                </td>
                                <td>CUS-06998.12</td>
                                <td>โตโยต้า แอท ยูไนเต็ด จำกัด</td>

                                <td id="tdSO68/004916">PARTIAL</td>
                                <td style="background-color:white">2025-03-13</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;"><a href="
                                    http://server_update:8000/sodetail?SONum=68/004915" style="color:black;">
                                    68/004915</a>
                                </td>
                                <td>CUS-09195</td>
                                <td>HMC Polymers Company Limited</td>

                                <td id="tdSO68/004915">PARTIAL</td>
                                <td style="background-color:white">2025-03-17</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;"><a href="
                                    http://server_update:8000/sodetail?SONum=68/004914" style="color:black;">
                                    68/004914</a>
                                </td>
                                <td>CUS-06023</td>
                                <td>วีไทร์ แอนด์ รับเบอร์ จำกัด</td>

                                <td id="tdSO68/004914">PARTIAL</td>
                                <td style="background-color:white">2025-03-16</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;"><a href="
                                    http://server_update:8000/sodetail?SONum=68/004913" style="color:black;">
                                    68/004913</a>
                                </td>
                                <td>CUS-06023</td>
                                <td>วีไทร์ แอนด์ รับเบอร์ จำกัด</td>

                                <td id="tdSO68/004913">PARTIAL</td>
                                <td style="background-color:white">2025-03-16</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:white;"><a href="
                                    http://server_update:8000/sodetail?SONum=68/004912" style="color:black;">
                                    68/004912</a>
                                </td>
                                <td>CUS-09330.1</td>
                                <td>พี.ซี.ทาคาชิมา (ประเทศไทย) จำกัด</td>

                                <td id="tdSO68/004912">PARTIAL</td>
                                <td style="background-color:white">2025-03-13</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:green;"><a href="
                                    http://server_update:8000/sodetail?SONum=68/004909" style="color:black;">
                                    68/004909</a>
                                </td>
                                <td>CUS-24565</td>
                                <td>เอ แอล เค อิมปอร์ต แอนด์ เอกซ์ปอร์ต จำกัด </td>

                                <td id="tdSO68/004909">PARTIAL</td>
                                <td style="background-color:white">2025-03-12</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;"><a href="
                                    http://server_update:8000/sodetail?SONum=68/004906" style="color:black;">
                                    68/004906</a>
                                </td>
                                <td>CUS-09219.1</td>
                                <td>เบียร์ทิพย์ บริวเวอรี่ (1991) จำกัด</td>

                                <td id="tdSO68/004906">PARTIAL</td>
                                <td style="background-color:white">2025-03-21</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;"><a href="
                                    http://server_update:8000/sodetail?SONum=68/004904" style="color:black;">
                                    68/004904</a>
                                </td>
                                <td>CUS-16809.2</td>
                                <td>แสงโสม จำกัด</td>

                                <td id="tdSO68/004904">PARTIAL</td>
                                <td style="background-color:white">2025-03-21</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;"><a href="
                                    http://server_update:8000/sodetail?SONum=68/004903" style="color:black;">
                                    68/004903</a>
                                </td>
                                <td>CUS-18998</td>
                                <td>ประมวลผล จำกัด</td>

                                <td id="tdSO68/004903">PARTIAL</td>
                                <td style="background-color:white">2025-03-21</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;"><a href="
                                    http://server_update:8000/sodetail?SONum=68/004902" style="color:black;">
                                    68/004902</a>
                                </td>
                                <td>CUS-18527</td>
                                <td>นทีชัย จำกัด</td>

                                <td id="tdSO68/004902">PARTIAL</td>
                                <td style="background-color:white">2025-03-21</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;"><a href="
                                    http://server_update:8000/sodetail?SONum=68/004901" style="color:black;">
                                    68/004901</a>
                                </td>
                                <td>CUS-18758</td>
                                <td>ไทยเบฟเวอเรจ เอ็นเนอร์ยี่ จำกัด </td>

                                <td id="tdSO68/004901">PARTIAL</td>
                                <td style="background-color:white">2025-03-21</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;"><a href="
                                    http://server_update:8000/sodetail?SONum=68/004900" style="color:black;">
                                    68/004900</a>
                                </td>
                                <td>CUS-13927</td>
                                <td>ไทยมาสเตอร์คัลเลอร์ จำกัด</td>

                                <td id="tdSO68/004900">PARTIAL</td>
                                <td style="background-color:white">2025-03-21</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:green;"><a href="
                                    http://server_update:8000/sodetail?SONum=68/004899" style="color:black;">
                                    68/004899</a>
                                </td>
                                <td>CUS-10173</td>
                                <td>ยูเนียน อินต้า จำกัด</td>

                                <td id="tdSO68/004899">FULL</td>
                                <td style="background-color:white">2025-03-11</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;"><a href="
                                    http://server_update:8000/sodetail?SONum=68/004898" style="color:black;">
                                    68/004898</a>
                                </td>
                                <td>CUS-13927</td>
                                <td>ไทยมาสเตอร์คัลเลอร์ จำกัด</td>

                                <td id="tdSO68/004898">PARTIAL</td>
                                <td style="background-color:white">2025-03-21</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:white;"><a href="
                                    http://server_update:8000/sodetail?SONum=68/004897" style="color:black;">
                                    68/004897</a>
                                </td>
                                <td>CUS-08521</td>
                                <td>พีเจที ยูไนเต็ด จำกัด</td>

                                <td id="tdSO68/004897">PARTIAL</td>
                                <td style="background-color:white">2025-03-30</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;"><a href="
                                    http://server_update:8000/sodetail?SONum=68/004896" style="color:black;">
                                    68/004896</a>
                                </td>
                                <td>CUS-07807</td>
                                <td>ROYAL TIME CITI CO., LTD.</td>

                                <td id="tdSO68/004896">PARTIAL</td>
                                <td style="background-color:white">2025-03-17</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;"><a href="
                                    http://server_update:8000/sodetail?SONum=68/004895" style="color:black;">
                                    68/004895</a>
                                </td>
                                <td>CUS-13927</td>
                                <td>ไทยมาสเตอร์คัลเลอร์ จำกัด</td>

                                <td id="tdSO68/004895">PARTIAL</td>
                                <td style="background-color:white">2025-03-21</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;"><a href="
                                    http://server_update:8000/sodetail?SONum=68/004894" style="color:black;">
                                    68/004894</a>
                                </td>
                                <td>CUS-13927</td>
                                <td>ไทยมาสเตอร์คัลเลอร์ จำกัด</td>

                                <td id="tdSO68/004894">PARTIAL</td>
                                <td style="background-color:white">2025-03-21</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;"><a href="
                                    http://server_update:8000/sodetail?SONum=68/004893" style="color:black;">
                                    68/004893</a>
                                </td>
                                <td>CUS-06766</td>
                                <td>ผลิตไฟฟ้าและน้ำเย็น จำกัด</td>

                                <td id="tdSO68/004893">PARTIAL</td>
                                <td style="background-color:white">2025-04-11</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;"><a href="
                                    http://server_update:8000/sodetail?SONum=68/004892" style="color:black;">
                                    68/004892</a>
                                </td>
                                <td>CUS-06671</td>
                                <td>ถิรไทย จำกัด (มหาชน)</td>

                                <td id="tdSO68/004892">PARTIAL</td>
                                <td style="background-color:white">2025-03-13</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:yellow;"><a href="
                                    http://server_update:8000/sodetail?SONum=68/004891" style="color:black;">
                                    68/004891</a>
                                </td>
                                <td>CUS-15065</td>
                                <td>ซาตาเก้ (ประเทศไทย) จำกัด</td>

                                <td id="tdSO68/004891">PARTIAL</td>
                                <td style="background-color:white">2025-04-24</td>
                            </tr>
                                                                                <tr>
                                <td scope="row" style="background-color:#FF99FF;"><a href="
                                    http://server_update:8000/sodetail?SONum=68/004890" style="color:black;">
                                    68/004890</a>
                                </td>
                                <td>CUS-19707</td>
                                <td>คอสมิค เอ็นจิเนียริ่ง แอนด์ ซัพพลาย จำกัด</td>

                                <td id="tdSO68/004890">FULL</td>
                                <td style="background-color:white">2025-03-15</td>
                            </tr>
                                                <script type="text/javascript">
                            getSOHD('SO68/004921^SO68/004920^SO68/004917^SO68/004916^SO68/004915^SO68/004914^SO68/004913^SO68/004912^SO68/004909^SO68/004906^SO68/004904^SO68/004903^SO68/004902^SO68/004901^SO68/004900^SO68/004899^SO68/004898^SO68/004897^SO68/004896^SO68/004895^SO68/004894^SO68/004893^SO68/004892^SO68/004891^SO68/004890')
                        </script>
                    </tbody>
                </table>
                
                <div class="d-flex justify-content-center">
                    <nav>
        <ul class="pagination">
            
                            <li class="page-item disabled" aria-disabled="true" aria-label="« Previous">
                    <span class="page-link" aria-hidden="true">‹</span>
                </li>
            
            
                            
                
                
                                                                                        <li class="page-item active" aria-current="page"><span class="page-link">1</span></li>
                                                                                                <li class="page-item"><a class="page-link" href="http://server_update:8000/solist?_token=ex7i8gpx2CqIgN6D6MtCQoNU2qBJMv0amRWw6mOg&amp;CreateBySale=0&amp;method=search&amp;perPage=25&amp;page=2">2</a></li>
                                                                                                <li class="page-item"><a class="page-link" href="http://server_update:8000/solist?_token=ex7i8gpx2CqIgN6D6MtCQoNU2qBJMv0amRWw6mOg&amp;CreateBySale=0&amp;method=search&amp;perPage=25&amp;page=3">3</a></li>
                                                                                                <li class="page-item"><a class="page-link" href="http://server_update:8000/solist?_token=ex7i8gpx2CqIgN6D6MtCQoNU2qBJMv0amRWw6mOg&amp;CreateBySale=0&amp;method=search&amp;perPage=25&amp;page=4">4</a></li>
                                                                                                <li class="page-item"><a class="page-link" href="http://server_update:8000/solist?_token=ex7i8gpx2CqIgN6D6MtCQoNU2qBJMv0amRWw6mOg&amp;CreateBySale=0&amp;method=search&amp;perPage=25&amp;page=5">5</a></li>
                                                                                                <li class="page-item"><a class="page-link" href="http://server_update:8000/solist?_token=ex7i8gpx2CqIgN6D6MtCQoNU2qBJMv0amRWw6mOg&amp;CreateBySale=0&amp;method=search&amp;perPage=25&amp;page=6">6</a></li>
                                                                                                <li class="page-item"><a class="page-link" href="http://server_update:8000/solist?_token=ex7i8gpx2CqIgN6D6MtCQoNU2qBJMv0amRWw6mOg&amp;CreateBySale=0&amp;method=search&amp;perPage=25&amp;page=7">7</a></li>
                                                                                                <li class="page-item"><a class="page-link" href="http://server_update:8000/solist?_token=ex7i8gpx2CqIgN6D6MtCQoNU2qBJMv0amRWw6mOg&amp;CreateBySale=0&amp;method=search&amp;perPage=25&amp;page=8">8</a></li>
                                                                                                <li class="page-item"><a class="page-link" href="http://server_update:8000/solist?_token=ex7i8gpx2CqIgN6D6MtCQoNU2qBJMv0amRWw6mOg&amp;CreateBySale=0&amp;method=search&amp;perPage=25&amp;page=9">9</a></li>
                                                                                                <li class="page-item"><a class="page-link" href="http://server_update:8000/solist?_token=ex7i8gpx2CqIgN6D6MtCQoNU2qBJMv0amRWw6mOg&amp;CreateBySale=0&amp;method=search&amp;perPage=25&amp;page=10">10</a></li>
                                                                                        
                                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">...</span></li>
                
                
                                            
                
                
                                                                                        <li class="page-item"><a class="page-link" href="http://server_update:8000/solist?_token=ex7i8gpx2CqIgN6D6MtCQoNU2qBJMv0amRWw6mOg&amp;CreateBySale=0&amp;method=search&amp;perPage=25&amp;page=5826">5826</a></li>
                                                                                                <li class="page-item"><a class="page-link" href="http://server_update:8000/solist?_token=ex7i8gpx2CqIgN6D6MtCQoNU2qBJMv0amRWw6mOg&amp;CreateBySale=0&amp;method=search&amp;perPage=25&amp;page=5827">5827</a></li>
                                                                        
            
                            <li class="page-item">
                    <a class="page-link" href="http://server_update:8000/solist?_token=ex7i8gpx2CqIgN6D6MtCQoNU2qBJMv0amRWw6mOg&amp;CreateBySale=0&amp;method=search&amp;perPage=25&amp;page=2" rel="next" aria-label="Next »">›</a>
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
                console.log(formElements[i].type)
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


async function checkForAccountAlerts() {
    try {
        const response = await fetch('/alertaccount/count'); 
        const data = await response.json();

        const badge = document.getElementById('alertAccountBadge');
        
        if (data.count > 0) {
            badge.textContent = data.count;
            badge.style.display = 'block';
        } else {
            badge.style.display = 'none';
        }
    } catch (error) {
        console.error('ไม่สามารถเช็คการแจ้งเตือนได้:', error);
    }
}

// เรียกตอนโหลดหน้า
checkForAccountAlerts();


setInterval(checkForAccountAlerts, 180000);

    </script>


</body></html>



