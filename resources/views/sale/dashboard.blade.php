{{-- resources/views/sale/dashboard.blade.php  (ข้อมูลจัดส่ง) — ธีมเดียวกับหน้า ชั้น SALE --}}
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลจัดส่ง</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    {{-- หมายเหตุ: ไม่โหลด css/dashboard.blade.css แล้ว (สไตล์ทั้งหมดอยู่ในไฟล์นี้) --}}
    <style>
        :root{
            --ink:#0f172a; --ink-700:#334155; --muted:#64748b; --faint:#94a3b8;
            --page-bg:#f8fafc; --soft:#f1f5f9; --border:#e2e8f0; --line:#e8edf3;
            --primary:#1a4fd6; --primary-dark:#123bb0; --primary-light:#eaf0fd;
            --success:#16a34a; --success-dark:#166534; --success-light:#dcfce7;
            --danger:#dc2626; --danger-dark:#b91c1c; --danger-light:#fee2e2;
            --warning:#b45309; --warning-light:#fef3c7;
            --shadow:0 1px 3px rgba(15,23,42,.04),0 4px 12px rgba(15,23,42,.05);
            --r-card:12px; --r-field:8px;
        }
        *{box-sizing:border-box;margin:0;padding:0}
        html{font-size:clamp(13px,0.3vw + 9px,16px)}
        html,body{background:var(--page-bg);font-family:'Sarabun','Segoe UI',Tahoma,sans-serif;color:var(--ink);line-height:1.5;-webkit-font-smoothing:antialiased}
        body{min-height:100vh;overflow-x:hidden}
        a{color:inherit}
        button{font:inherit;cursor:pointer}
        svg.i{width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;flex:none}

        /* ===== แถบบน (หัว + ตัวกรอง แถวเดียว) ===== */
        .top-banner{display:flex;align-items:center;gap:14px;flex-wrap:wrap;background:#fff;border:1px solid var(--line);border-radius:var(--r-card);
                    margin:15px 15px 12px;padding:12px 16px 12px 18px;box-shadow:var(--shadow);position:sticky;top:10px;z-index:100}
        .top-banner .h1{flex:none;display:flex;align-items:center;gap:10px;font-weight:800;font-size:clamp(18px,.8vw + 9px,22px);letter-spacing:-.3px;white-space:nowrap;color:var(--ink);text-decoration:none}
        .top-banner .logo{width:38px;height:38px;border-radius:10px;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 10px rgba(26,79,214,.28)}
        .top-banner .logo svg{width:19px;height:19px}
        .vsep{width:1px;height:28px;background:var(--line);flex:none}
        .tools{flex:1 1 760px;display:flex;align-items:center;gap:8px;min-width:0}
        .tools form{display:contents}
        .banner-right{display:flex;align-items:center;gap:8px;margin-left:auto;flex-shrink:0}

        .fld{height:40px;display:flex;align-items:center;gap:8px;padding:0 12px;background:var(--page-bg);border:1px solid var(--line);border-radius:var(--r-field);transition:.15s;min-width:0;position:relative}
        .fld:hover{border-color:var(--border)}
        .fld:focus-within{background:#fff;border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light)}
        .fld > svg{color:var(--faint)}
        .fld:has(.lbl){align-items:baseline}
        .fld:has(.lbl) > svg,.fld .caret{align-self:center}
        .fld .lbl{font-size:.82rem;font-weight:600;color:var(--faint);white-space:nowrap}
        .fld input,.fld select{border:0;outline:0;background:transparent;font:inherit;font-size:.95rem;color:var(--ink);height:100%;min-width:0;padding:0;flex:1;width:100%}
        .fld input::placeholder{color:var(--faint)}
        .fld select{appearance:none;-webkit-appearance:none;cursor:pointer;padding-right:22px}
        .fld .caret{position:absolute;right:10px;color:var(--muted);pointer-events:none}
        .fld .kbd{flex:none;font-size:.7rem;font-weight:600;color:var(--faint);border:1px solid var(--border);border-radius:5px;padding:0 5px;line-height:17px;background:#fff}
        .fld:focus-within .kbd{color:var(--primary);border-color:#c7d6fb}
        .fld.has-val{background:#fff;border-color:#c7d6fb}
        .f-date{flex:0 0 auto}
        .f-date input{width:132px}
        .f-emp{flex:0 1 210px;cursor:pointer;user-select:none}
        /* ===== ดรอปดาวน์ผู้บันทึก (หน้าตาเดียวกับหน้า ชั้น SALE) ===== */
        .f-emp.dd-ready select{display:none}
        .f-emp:not(.dd-ready) .dd-btn{display:none}
        .dd-btn{flex:1;min-width:0;height:100%;border:0;background:transparent;padding:0 20px 0 0;text-align:left;font:inherit;font-size:.95rem;color:var(--ink);cursor:pointer;
                white-space:nowrap;overflow:hidden;text-overflow:ellipsis;outline:0}
        .f-emp .caret svg{transition:transform .15s}
        .f-emp.open{background:#fff;border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light)}
        .f-emp.open .caret svg{transform:rotate(180deg)}
        .dd-panel{display:none;position:absolute;left:0;top:calc(100% + 6px);min-width:100%;width:260px;max-width:92vw;z-index:300;background:#fff;border:1px solid var(--border);
                  border-radius:12px;box-shadow:0 12px 32px rgba(15,23,42,.16);padding:6px;cursor:default}
        .f-emp.open .dd-panel{display:block}
        .dd-search{display:flex;align-items:center;gap:8px;height:38px;padding:0 10px;margin-bottom:4px;border:1px solid var(--line);border-radius:8px;background:var(--page-bg);color:var(--faint)}
        .dd-search:focus-within{background:#fff;border-color:var(--faint)}
        .dd-search input{flex:1;min-width:0;border:0;outline:0;background:transparent;font:inherit;font-size:.9rem;color:var(--ink)}
        .dd-list{max-height:min(340px,55vh);overflow-y:auto}
        .dd-item{display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:8px;font-size:.92rem;color:var(--ink);cursor:pointer;white-space:nowrap}
        .dd-item:hover,.dd-item.hl{background:var(--soft)}
        .dd-item .ck{width:15px;height:15px;visibility:hidden;color:var(--primary)}
        .dd-item.sel{font-weight:700;background:var(--primary-light);color:var(--primary)}
        .dd-item.sel .ck{visibility:visible}
        .dd-sep{height:1px;background:var(--line);margin:4px 6px}
        .dd-empty{padding:14px;text-align:center;color:var(--muted);font-size:.88rem}
        .f-so,.f-bill{flex:1 1 210px}

        .btn{height:40px;display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:0 15px;border-radius:var(--r-field);border:1px solid var(--line);
             background:#fff;color:var(--ink-700);font-size:.92rem;font-weight:600;text-decoration:none;white-space:nowrap;transition:.15s}
        .btn:hover{background:var(--soft);color:var(--ink);border-color:var(--border)}
        .btn svg{width:17px;height:17px}
        .btn-icon{width:40px;padding:0;color:var(--muted)}
        .btn-clear:hover{color:var(--danger);border-color:#fecaca;background:#fef2f2}
        /* ปุ่มงานค้าง (แบบเดียวกับป้าย "เลยกำหนด" ของหน้า ชั้น SALE) */
        @keyframes pdPulse{0%,100%{box-shadow:0 0 0 0 rgba(220,38,38,.35)}50%{box-shadow:0 0 0 6px rgba(220,38,38,0)}}
        .btn-pending{height:40px;display:inline-flex;align-items:center;gap:9px;padding:0 12px 0 8px;border-radius:var(--r-field);border:1px solid #fecaca;
                     background:linear-gradient(180deg,#fff5f5,#fee2e2);color:var(--danger-dark);font-weight:700;font-size:.95rem;text-decoration:none;white-space:nowrap;
                     animation:pdPulse 1.8s ease-in-out infinite;transition:background .15s,color .15s,border-color .15s,transform .12s}
        .btn-pending .pd-ic{width:24px;height:24px;border-radius:50%;background:var(--danger);color:#fff;display:inline-flex;align-items:center;justify-content:center;
                            font-weight:800;font-size:.9rem;box-shadow:0 2px 6px rgba(220,38,38,.35)}
        .btn-pending .pd-go{width:16px;height:16px;opacity:.7;transition:transform .15s,opacity .15s}
        .btn-pending:hover{background:var(--danger);border-color:var(--danger);color:#fff;transform:translateY(-1px);animation:none}
        .btn-pending:hover .pd-ic{background:#fff;color:var(--danger)}
        .btn-pending:hover .pd-go{transform:translateX(3px);opacity:1}
        @media (prefers-reduced-motion:reduce){.btn-pending{animation:none}}
        /* ปุ่มค้นหา (แบบเดียวกับหน้า ชั้น SALE) */
        .search-btn{height:40px;flex:none;display:inline-flex;align-items:center;gap:8px;padding:0 12px 0 16px;border:0;border-radius:var(--r-field);background:var(--primary);color:#fff;
                    font-size:1.02rem;font-weight:700;white-space:nowrap;box-shadow:0 1px 0 rgba(255,255,255,.22) inset,0 3px 10px rgba(26,79,214,.20);transition:transform .12s,background .15s}
        .search-btn:hover{background:var(--primary-dark);transform:translateY(-1px)}
        .search-btn:active{transform:none}
        .search-btn .sb-ic{display:inline-flex}
        .search-btn .sb-ic svg{width:19px;height:19px}
        .search-btn .sb-kbd{font:inherit;font-size:.76rem;font-weight:600;color:rgba(255,255,255,.9);background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.28);border-radius:6px;padding:1px 6px;line-height:18px}
        .user-badge{height:40px;display:inline-flex;align-items:center;gap:8px;padding:0 12px 0 5px;border-radius:999px;background:var(--page-bg);border:1px solid var(--line);font-size:.86rem;color:var(--ink-700);white-space:nowrap}
        .user-badge .av{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:var(--soft);color:var(--ink)}
        .user-badge .av svg{width:15px;height:15px}

        main{padding:0 15px 24px}

        /* ===== แถบสรุปเหนือตาราง ===== */
        .summary:not(:has(.chip)){display:none}   /* แสดงเฉพาะตอนมีคำค้น */
        .summary{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin:0 2px 10px}
        .summary .count{font-size:.92rem;color:var(--muted)}
        .summary .count b{color:var(--ink);font-size:1.02rem}
        .chip{display:inline-flex;align-items:center;gap:7px;height:32px;padding:0 6px 0 12px;border-radius:999px;background:var(--primary-light);color:var(--primary);
              font-size:.86rem;font-weight:600;border:1px solid #c7d6fb}
        .chip a{width:22px;height:22px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:var(--primary);text-decoration:none}
        .chip a:hover{background:#fff}
        .chip a svg{width:13px;height:13px}
        .legend{margin-left:auto;display:flex;gap:6px;flex-wrap:wrap}

        /* ===== ตาราง ===== */
        .table-card{background:#fff;border:1px solid var(--line);border-radius:var(--r-card);box-shadow:var(--shadow);overflow:hidden}
        .table-scroll{overflow-x:auto}
        table.grid{width:100%;border-collapse:collapse;min-width:1100px}
        /* หัวตารางสีฟ้า + เส้นตาราง */
        .grid th{position:sticky;top:0;z-index:2;background:var(--primary);color:#fff;font-size:.84rem;font-weight:700;text-align:center;padding:12px 12px;
                 border-right:1px solid rgba(255,255,255,.18);white-space:nowrap}
        .grid th:last-child{border-right:0}
        .grid td{padding:12px 12px;border-bottom:1px solid var(--border);border-right:1px solid var(--line);font-size:.92rem;vertical-align:middle;text-align:center}
        .grid td.c-cust{text-align:left}   /* ชื่อลูกค้ายาว ชิดซ้ายอ่านง่ายกว่า */
        .grid td .cust{margin:0}
        .grid td:last-child{border-right:0}
        .grid tbody tr{transition:background .12s}
        .grid tbody tr:nth-child(even){background:#fafbfd}
        .grid tbody tr:hover{background:#eef3fe}
        .grid tbody tr:last-child td{border-bottom:0}
        .grid td.c-no{color:var(--faint)}
        .grid .c-no{width:52px;text-align:center;font-variant-numeric:tabular-nums}
        .num{font-variant-numeric:tabular-nums;white-space:nowrap}
        .muted{color:var(--muted)}
        .sub{display:block;font-size:.78rem;color:var(--faint);margin-top:2px;white-space:nowrap}

        /* เลขใบส่งของ: เปิด PDF ได้ = ลิงก์เขียว ; จัดส่งแล้ว (statusdeli=1) = ป้ายพื้นเขียว */
        .bill{display:inline-flex;align-items:center;gap:6px;font-weight:700;white-space:nowrap;padding:3px 9px;border-radius:7px;color:var(--ink)}
        a.bill{color:var(--success-dark);text-decoration:none}
        a.bill:hover{text-decoration:underline}
        .bill.deli{background:var(--success-light);box-shadow:inset 0 0 0 1px #a7e3b9}
        .bill.deli::after{content:"ส่งแล้ว";font-size:.68rem;font-weight:700;color:#fff;background:var(--success);border-radius:999px;padding:0 6px;line-height:16px}
        a.so-link{color:var(--primary);font-weight:700;text-decoration:none;white-space:nowrap}
        a.so-link:hover{text-decoration:underline}
        .cust{min-width:200px;max-width:320px;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
        .btype{display:inline-flex;align-items:center;gap:6px;white-space:nowrap}
        .mini-btn{width:24px;height:24px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;background:var(--primary-light);color:var(--primary);text-decoration:none;flex:none}
        .mini-btn:hover{background:var(--primary);color:#fff}
        .mini-btn svg{width:14px;height:14px}

        /* ประเภทงาน: ตัวหนังสือธรรมดา ไม่มีสี */
        .ftype{display:inline-block;font-size:.88rem;line-height:1.4;color:var(--ink-700);max-width:170px}

        /* สถานะ */
        .pill{display:inline-flex;align-items:center;gap:6px;padding:3px 11px;border-radius:999px;font-size:.8rem;font-weight:700;white-space:nowrap;border:0;font-family:inherit}
        .pill::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
        .st-doing{background:var(--primary-light);color:var(--primary)}
        .st-cancel{background:var(--danger-light);color:var(--danger-dark)}
        .st-noprint{background:var(--soft);color:var(--muted)}
        .st-done{background:var(--success-light);color:var(--success-dark);cursor:pointer;transition:.15s}
        .st-done:hover{background:var(--success);color:#fff}
        .st-done.has-deli{background:#fef08a;color:#5a4b00}
        .st-done.has-deli::before{display:none}
        .st-done.has-deli:hover{background:#facc15;color:#422006}
        .st-done svg{width:13px;height:13px}

        .btn-more{height:32px;display:inline-flex;align-items:center;gap:6px;padding:0 11px;border-radius:7px;border:1px solid var(--line);background:#fff;color:var(--ink-700);font-size:.84rem;font-weight:600;text-decoration:none;white-space:nowrap}
        .btn-more:hover{background:var(--primary);border-color:var(--primary);color:#fff}
        .btn-more svg{width:15px;height:15px}

        .empty{padding:60px 20px;text-align:center;color:var(--muted)}
        .empty svg{width:44px;height:44px;color:var(--border);stroke-width:1.5;margin-bottom:10px}

        /* ===== ป๊อปอัพ ===== */
        .popup-overlay{display:none;position:fixed;inset:0;z-index:300;background:rgba(15,23,42,.55);align-items:center;justify-content:center;padding:20px}
        .popup-content{position:relative;background:#fff;border-radius:16px;width:min(94vw,980px);max-height:88vh;overflow:auto;box-shadow:0 24px 60px rgba(0,0,0,.28);animation:pop .15s ease-out}
        @keyframes pop{from{opacity:0;transform:translateY(8px) scale(.98)}to{opacity:1;transform:none}}
        .pp-head{position:sticky;top:0;z-index:1;display:flex;align-items:center;gap:12px;padding:16px 20px;background:#fff;border-bottom:1px solid var(--line)}
        .pp-head .ic{width:40px;height:40px;border-radius:10px;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center}
        .pp-head .ic svg{width:19px;height:19px}
        .pp-head h3{font-size:1.12rem;font-weight:700}
        .pp-head .sub{font-size:.85rem}
        .close-btn{margin-left:auto;width:38px;height:38px;border-radius:9px;border:0;background:none;color:var(--muted);display:flex;align-items:center;justify-content:center;cursor:pointer}
        .close-btn:hover{background:var(--soft);color:var(--ink)}
        .close-btn svg{width:20px;height:20px}
        .pp-body{padding:16px 20px 20px}
        .pp-sec{font-size:.82rem;font-weight:700;color:var(--muted);margin:4px 0 8px;display:flex;align-items:center;gap:6px}
        .pp-sec svg{width:15px;height:15px}
        .pp-table{width:100%;border-collapse:separate;border-spacing:0;border:1px solid var(--line);border-radius:10px;overflow:hidden;margin-bottom:16px}
        .pp-table th{background:var(--page-bg);color:#475569;font-size:.8rem;font-weight:700;text-align:left;padding:9px 12px;border-bottom:1px solid var(--line)}
        .pp-table td{padding:10px 12px;border-bottom:1px solid var(--line);font-size:.9rem;vertical-align:top}
        .pp-table tbody tr:last-child td{border-bottom:0}
        .pp-table td a{color:var(--primary);font-weight:600}
        .pp-table .r{text-align:right}
        #popup-body-3{width:100%;min-height:80px;resize:vertical;border:1px solid var(--line);border-radius:10px;background:var(--page-bg);padding:10px 12px;font:inherit;font-size:.92rem;color:var(--ink);outline:0}

        /* ===== แบ่งหน้า ===== */
        nav[role="navigation"] p{display:none !important}
        nav[role="navigation"] .sm\:hidden{display:none !important}
        .pagination-wrap{display:flex;justify-content:center;margin:18px 0 6px}
        nav[role="navigation"] > div{display:flex !important;align-items:center !important;justify-content:center !important;gap:12px;flex-wrap:nowrap !important}
        nav[role="navigation"] a[rel="prev"] span,nav[role="navigation"] a[rel="next"] span{display:none !important}
        nav[role="navigation"] a[rel="prev"],nav[role="navigation"] a[rel="next"]{width:38px;height:38px;border-radius:50%;background:#fff;border:1px solid var(--line);display:flex;align-items:center;justify-content:center;color:var(--ink-700);box-shadow:var(--shadow)}
        nav[role="navigation"] a[rel="prev"]:hover,nav[role="navigation"] a[rel="next"]:hover{background:var(--primary);color:#fff;border-color:var(--primary)}
        nav[role="navigation"] svg{width:18px !important;height:18px !important}
        nav[role="navigation"] a.relative,nav[role="navigation"] span.relative{min-width:36px;height:36px;padding:0 6px;display:flex;align-items:center;justify-content:center;font-size:.95rem;font-weight:600;color:var(--ink-700);background:transparent !important;border:none !important;border-radius:9px;text-decoration:none}
        nav[role="navigation"] a.relative:hover{background:var(--primary-light) !important;color:var(--primary)}
        nav[role="navigation"] .hidden.sm\:flex-1,nav[role="navigation"] > div:last-child{display:flex !important}
        nav[role="navigation"] span.z-0{display:inline-flex !important;align-items:center;gap:4px;background:#fff;border:1px solid var(--line);border-radius:999px;padding:4px;box-shadow:var(--shadow)}
        nav[role="navigation"] span.z-0 a[rel="prev"],nav[role="navigation"] span.z-0 a[rel="next"]{box-shadow:none;border:0;background:var(--soft)}
        nav[role="navigation"] svg{fill:currentColor}
        nav[role="navigation"] span[aria-current="page"] span{background:var(--primary) !important;color:#fff !important;border-radius:9px;font-weight:800}

        /* ===== แจ้งเตือนเล็ก ===== */
        .ui-toast{position:fixed;left:50%;bottom:24px;z-index:450;transform:translate(-50%,20px);opacity:0;pointer-events:none;transition:.2s;width:max-content;max-width:92vw;
                  display:flex;align-items:center;gap:10px;background:var(--ink);color:#fff;padding:12px 18px;border-radius:12px;font-size:.92rem;font-weight:500;box-shadow:0 10px 30px rgba(0,0,0,.25)}
        .ui-toast.show{opacity:1;transform:translate(-50%,0)}
        .ui-toast.t-error{background:var(--danger)}

        /* ===== จอขนาดกลาง / เล็ก ===== */
        @media (max-width:1500px){ .tools{order:3;flex-basis:100%} .vsep{display:none} }
        @media (max-width:1000px){
            .top-banner{position:static}
            .tools{flex-wrap:wrap}
            .f-date,.f-emp{flex:1 1 calc(50% - 4px)}
            .f-date input{width:100%}
            .f-so,.f-bill{flex:1 1 calc(50% - 4px)}
            .tools .search-btn{flex:1 1 auto;justify-content:center;height:44px}
            .tools .btn-clear{flex:none;height:44px;width:44px}
            .legend{margin-left:0}
            /* ตาราง -> การ์ดทีละบิล */
            table.grid{min-width:0}
            .grid thead{display:none}
            .grid,.grid tbody{display:block}
            .grid tbody tr{display:grid;grid-template-columns:1fr 1fr;gap:8px 14px;padding:14px 16px;border-bottom:1px solid var(--line)}
            .grid tbody tr:hover{background:#fff}
            .grid td{display:block;border:0;padding:0;text-align:left}
            .grid td[data-label]::before{content:attr(data-label);display:block;font-size:.72rem;font-weight:600;color:var(--faint);margin-bottom:2px}
            .grid td.c-no{display:none}
            .grid td.c-cust,.grid td.c-more{grid-column:1/-1}
            .cust{max-width:none}
            .btn-more{width:100%;justify-content:center;height:38px}
        }
        @media (max-width:560px){
            .user-badge .name{display:none}
            .banner-right{width:100%}
            .banner-right .btn-pending{flex:1;justify-content:center}
            .f-date,.f-emp,.f-so,.f-bill{flex-basis:100%}
            .search-btn .sb-kbd{display:none}
        }
    </style>
</head>
<body>

    <div class="top-banner">
        <a class="h1" href="" title="รีเฟรชหน้า"><span class="logo"><svg class="i" viewBox="0 0 24 24"><path d="M1 4h14v12H1zM15 8h4l3 3v5h-7z"/><circle cx="5.5" cy="18.5" r="2"/><circle cx="18.5" cy="18.5" r="2"/></svg></span>ข้อมูลจัดส่ง</a>
        <span class="vsep"></span>

        <div class="tools">
            {{-- กรองวันที่ / ผู้บันทึก : เปลี่ยนแล้วค้นทันที (ฟอร์มเดิม) --}}
            <form method="GET" action="{{ route('sale.dashboard') }}" class="filter-form" id="autoSearchForm">
                <label class="fld f-date" for="date" title="วันที่ (เดือน / วัน / ปี)">
                    <svg class="i" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    <input type="date" id="date" name="date" value="{{ request('date') }}">
                </label>
                <div class="fld f-emp {{ request('emp_name') ? 'has-val' : '' }}" id="empBox" title="ผู้บันทึก">
                    <span class="lbl">ผู้บันทึก</span>
                    {{-- select เดิมยังเป็นตัวเก็บค่า/ส่งฟอร์ม ; ดรอปดาวน์ด้านล่างแสดงแทน --}}
                    <select name="emp_name" id="emp_name" onchange="document.getElementById('autoSearchForm').submit();">
                        <option value="">ทั้งหมด</option>
                        @foreach($empList as $emp)
                            <option value="{{ $emp }}" {{ request('emp_name') == $emp ? 'selected' : '' }}>{{ $emp }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="dd-btn" id="empBtn" aria-haspopup="listbox"><span id="empText">ทั้งหมด</span></button>
                    <span class="caret"><svg class="i" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg></span>
                    <div class="dd-panel" id="empPanel" role="listbox">
                        <div class="dd-search"><svg class="i" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg><input type="text" id="empFilter" placeholder="พิมพ์ชื่อผู้บันทึก..." autocomplete="off"></div>
                        <div class="dd-list" id="empList"></div>
                    </div>
                </div>
                <input type="hidden" name="create_by" value="{{ request('create_by') }}">
                {{-- ✅ ไม่ส่ง keyword / so_keyword ต่อ: เปลี่ยนวันที่ = เริ่มกรองวันใหม่ ไม่ติดคำค้นเดิม --}}
                <button type="submit" style="display:none;">ค้นหา</button>
            </form>

            {{-- ✅ ค้นหา อ้างอิงใบสั่งขาย: ไม่ส่ง date / emp_name → ค้นได้ทุกวัน (กด Enter) --}}
            <form method="GET" action="{{ route('sale.dashboard') }}" class="search-box">
                <label class="fld f-so {{ request('so_keyword') ? 'has-val' : '' }}" title="พิมพ์เลขใบสั่งขาย แล้วกด Enter (ค้นทุกวันที่)">
                    <svg class="i" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                    <span class="lbl">SO</span>
                    <input type="text" name="so_keyword" placeholder="ค้นหา อ้างอิงใบสั่งขาย" value="{{ request('so_keyword') }}" autocomplete="off">
                </label>
                <input type="hidden" name="create_by" value="{{ request('create_by') }}">
            </form>

            {{-- ✅ ค้นหา เลขที่บิล: ไม่ส่ง date / emp_name → ค้นได้ทุกวัน (กด Enter) --}}
            <form method="GET" action="{{ route('sale.dashboard') }}" class="search-box">
                <label class="fld f-bill {{ request('keyword') ? 'has-val' : '' }}" title="พิมพ์เลขที่บิล แล้วกด Enter (ค้นทุกวันที่)">
                    <svg class="i" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                    <span class="lbl">บิล</span>
                    <input type="text" name="keyword" placeholder="ค้นหา เลขที่บิล" value="{{ request('keyword') }}" autocomplete="off">
                </label>
                <input type="hidden" name="create_by" value="{{ request('create_by') }}">
            </form>

            <button type="button" class="search-btn" id="btnSearch" title="ค้นหา (หรือกด Enter ในช่องค้นหา)">
                <span class="sb-ic"><svg class="i" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg></span>
                <span class="sb-txt">ค้นหา</span>
                <kbd class="sb-kbd">Enter</kbd>
            </button>

            {{-- ปุ่มล้างทั้งหมด: ล้างทุกช่อง + วันที่กลับเป็นวันนี้ --}}
            <a href="{{ route('sale.dashboard', ['date' => now()->format('Y-m-d'), 'create_by' => request('create_by')]) }}"
               class="btn btn-icon btn-clear" title="ล้างตัวกรองทั้งหมด (วันที่กลับเป็นวันนี้)"><svg class="i" viewBox="0 0 24 24"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg></a>
        </div>

        <div class="banner-right">
            <a href="alertbill" class="btn-pending" title="ดูงานค้าง"><span class="pd-ic">!</span><span class="pd-txt">งานค้าง</span><svg class="i pd-go" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
            <span class="user-badge" title="ผู้ใช้งาน"><span class="av"><svg class="i" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span><span class="name">{{ request()->get('create_by', 'Guest') }}</span></span>
            <a href="http://server_update:8000/solist" class="btn btn-icon" title="หน้าหลัก"><svg class="i" viewBox="0 0 24 24"><path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/></svg></a>
        </div>
    </div>

    <script>
// ===== ดรอปดาวน์ผู้บันทึก: อ่านตัวเลือกจาก select เดิม แล้วเลือกค่าเข้า select (ส่งฟอร์มเหมือนเดิม) =====
(function () {
    const box = document.getElementById('empBox'), sel = document.getElementById('emp_name');
    const btn = document.getElementById('empBtn'), txt = document.getElementById('empText');
    const list = document.getElementById('empList'), filter = document.getElementById('empFilter');
    if (!box || !sel) return;
    box.classList.add('dd-ready');
    const ICK = '<svg class="i ck" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>';
    const esc = v => String(v).replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
    let hl = -1;
    function render() {
        const q = filter.value.trim().toLowerCase();
        const opts = Array.from(sel.options);
        const item = o => '<div class="dd-item' + (o.value === sel.value ? ' sel' : '') + '" role="option" data-val="' + esc(o.value) + '">' + ICK + esc(o.textContent.trim()) + '</div>';
        const all = opts.find(o => o.value === '');
        const rest = opts.filter(o => o.value !== '' && (!q || o.textContent.toLowerCase().indexOf(q) !== -1));
        list.innerHTML = ((all && !q) ? item(all) + '<div class="dd-sep"></div>' : '') + (rest.map(item).join('') || (q ? '<div class="dd-empty">ไม่พบชื่อ</div>' : ''));
        hl = -1;
    }
    function sync() { const o = sel.options[sel.selectedIndex]; txt.textContent = o ? o.textContent.trim() : 'ทั้งหมด'; }
    function open() { filter.value = ''; render(); box.classList.add('open'); setTimeout(() => filter.focus(), 0);
                      const s = list.querySelector('.sel'); if (s) s.scrollIntoView({ block: 'nearest' }); }
    function close() { box.classList.remove('open'); }
    function pick(v) { close(); if (sel.value === v) return; sel.value = v; sync(); sel.dispatchEvent(new Event('change')); }
    box.addEventListener('mousedown', e => {
        if (e.target.closest('.dd-search')) return;
        const it = e.target.closest('.dd-item');
        e.preventDefault();
        if (it) { pick(it.dataset.val); return; }
        box.classList.contains('open') ? close() : open();
    });
    filter.addEventListener('input', render);
    const keys = e => {
        const items = Array.from(list.querySelectorAll('.dd-item'));
        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            e.preventDefault();
            if (!box.classList.contains('open')) { open(); return; }
            hl = e.key === 'ArrowDown' ? Math.min(hl + 1, items.length - 1) : Math.max(hl - 1, 0);
            items.forEach((it, i) => it.classList.toggle('hl', i === hl));
            if (items[hl]) items[hl].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (!box.classList.contains('open')) { open(); return; }
            const it = items[hl] || (filter.value.trim() && items.length === 1 ? items[0] : null);
            if (it) pick(it.dataset.val);
        } else if (e.key === 'Escape') { close(); btn.focus(); }
    };
    btn.addEventListener('keydown', keys);
    filter.addEventListener('keydown', keys);
    document.addEventListener('mousedown', e => { if (!box.contains(e.target)) close(); });
    sync();
})();

// ปุ่มค้นหา: ส่งฟอร์มของช่องที่มีคำค้น (SO ก่อน แล้วเลขบิล) — ไม่มีคำค้น = ค้นตามวันที่/ผู้บันทึก
document.getElementById('btnSearch').addEventListener('click', () => {
    const so   = document.querySelector('input[name="so_keyword"]');
    const bill = document.querySelector('input[name="keyword"]');
    if (so && so.value.trim())        so.form.submit();
    else if (bill && bill.value.trim()) bill.form.submit();
    else document.getElementById('autoSearchForm').submit();
});
const form = document.getElementById('autoSearchForm');
const dateInput = document.getElementById('date');

dateInput.addEventListener('change', () => {
    form.submit();
});

// ✅ auto-submit เฉพาะตอนเข้าหน้าเปล่า ๆ เท่านั้น
//    ถ้ามี keyword / so_keyword / date / emp_name อยู่แล้ว = ห้ามยิงทับ (เดิมมันยัดวันที่วันนี้ทับผลค้นหา)
window.addEventListener('load', () => {
    const p = new URLSearchParams(location.search);
    if (p.get('keyword') || p.get('so_keyword') || p.get('date') || p.get('emp_name')) return;

    if (!sessionStorage.getItem('hasAutoSubmitted')) {
        sessionStorage.setItem('hasAutoSubmitted', 'true');
        if (!dateInput.value) {
            dateInput.value = new Date().toISOString().slice(0, 10);
        }
        form.submit();
    }
});
    </script>

    {{-- map สีประเภทงาน --}}
    @php
        $formTypeMap = [
            'บิล/PO3'                       => 'bg-red',
            'บิล/PO3/วางบิล'                => 'bg-green',
            'บิล/PO3/วางบิล/สำเนาหน้าบิล2' => 'bg-blue',
            'บิล/PO3/สำเนาหน้าบิล2'         => 'bg-purple',
            'บิล/PO3/บัญชี'                 => 'bg-yellow',
        ];
    @endphp

    <main>
        <div class="summary">
            @if(request('so_keyword'))
                <span class="chip">ใบสั่งขาย “{{ request('so_keyword') }}” · ทุกวันที่
                    <a href="{{ route('sale.dashboard', ['date' => now()->format('Y-m-d'), 'create_by' => request('create_by')]) }}" title="ล้างคำค้น"><svg class="i" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg></a></span>
            @endif
            @if(request('keyword'))
                <span class="chip">เลขที่บิล “{{ request('keyword') }}” · ทุกวันที่
                    <a href="{{ route('sale.dashboard', ['date' => now()->format('Y-m-d'), 'create_by' => request('create_by')]) }}" title="ล้างคำค้น"><svg class="i" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg></a></span>
            @endif
        </div>

        <div class="table-card">
            <div class="table-scroll">
                <table class="grid">
                    <thead>
                        <tr>
                            <th class="c-no">ลำดับ</th>
                            <th>อ้างอิงใบส่งของ</th>
                            <th>อ้างอิงใบสั่งขาย</th>
                            <th>อ้างอิงใบสั่งซื้อ</th>
                            {{-- ✅ ลบ REF ออกจากตารางหลัก --}}
                            <th>ชื่อลูกค้า</th>
                            <th>วันที่จัดส่ง</th>
                            <th>ผู้บันทึก</th>
                            <th>ประเภทบิล</th>
                            <th>บันทึกลงระบบ</th>
                            <th>ประเภทงาน</th>
                            <th>สถานะ</th>
                            <th>ข้อมูลสินค้า</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">

                    @foreach($bill as $item)
@php
    $pdfPath = "doc_document/{$item->billid}.pdf";
    $billPath = "bill_document/{$item->billid}.pdf";
    $hasPdf  = \Illuminate\Support\Facades\Storage::disk('public')->exists($pdfPath);
    $isNoMerge = ($item->customer_id === 'CUS-26039');
@endphp
                        <tr>
                            <td class="c-no">{{ ($bill->currentPage() - 1) * $bill->perPage() + $loop->iteration }}</td>

                            <td class="col-billid" data-label="อ้างอิงใบส่งของ">
                                @if($hasPdf)
                                    @if($isNoMerge)
                                        <a class="bill @if($item->statusdeli == 1) deli @endif" href="{{ asset('storage/doc_document/' . $item->billid . '.pdf') }}" target="_blank" title="เปิดเอกสาร PDF">{{ $item->billid }}</a>
                                    @else
                                        <a class="bill @if($item->statusdeli == 1) deli @endif" href="javascript:void(0);" onclick="mergeAndOpenPdfs('{{ $item->billid }}')" title="เปิดเอกสาร PDF">{{ $item->billid }}</a>
                                    @endif
                                @elseif($item->statusdeli == 1)
                                    <a class="bill deli" href="https://drive.google.com/drive/u/0/search?q={{ $item->billid }}+parent:1WyDB1b01cDQ53Ap7B03UIGFbL6a2Y6WB" target="_blank" title="เปิดใน Google Drive">{{ $item->billid }}</a>
                                @else
                                    <span class="bill">{{ $item->billid }}</span>
                                @endif
                            </td>

                            <td data-label="อ้างอิงใบสั่งขาย">
                                <a class="so-link" href="http://server_update:8000/sodetail?SONum={{ urlencode($item->so_id) }}" target="_blank" rel="noopener">{{ $item->so_id }}</a>
                            </td>

                            <td class="num muted" data-label="อ้างอิงใบสั่งซื้อ">{{ $item->ponum }}</td>

                            {{-- ✅ ลบ REF ออกจากตารางหลัก (แต่ยังส่งเข้า Popup อยู่) --}}
                            <td class="c-cust" data-label="ชื่อลูกค้า"><div class="cust" title="{{ $item->customer_name }}">{{ $item->customer_name }}</div></td>

                            <td class="num" data-label="วันที่จัดส่ง">{{ \Carbon\Carbon::parse($item->date_of_dali)->format('d/m/Y') }}</td>
                            <td data-label="ผู้บันทึก">{{ $item->emp_name }}</td>

                            <td data-label="ประเภทบิล">
                                <span class="btype">{{ $item->billtype }}
                                @if($hasPdf)
                                    @if($isNoMerge)
                                        <a class="mini-btn" href="{{ asset('storage/bill_document/' . $item->billid . '.pdf') }}" target="_blank" title="เปิดไฟล์ใบเสร็จ"><svg class="i" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg></a>
                                    @else
                                        <a class="mini-btn" href="javascript:void(0);" onclick="openBillOnly('{{ $item->billid }}')" title="เปิดไฟล์ใบเสร็จ"><svg class="i" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg></a>
                                    @endif
                                @endif
                                </span>
                            </td>

                            <td class="num" data-label="บันทึกลงระบบ">{{ \Carbon\Carbon::parse($item->time)->format('d/m/Y') }}<span class="sub">{{ \Carbon\Carbon::parse($item->time)->format('H:i') }} น.</span></td>

                            {{-- ประเภทงาน: ป้ายสีตาม map --}}
                            <td class="col-type" data-label="ประเภทงาน"><span class="ftype {{ $formTypeMap[$item->formtype] ?? '' }}">{{ $item->formtype }}</span></td>

                            {{-- สถานะ --}}
                            <td class="col-status" data-label="สถานะ">
                            @if($item->statuspdf == 0)
                                <span class="pill st-doing">กำลังดำเนินการ</span>
                            @elseif($item->statuspdf == 6)
                                <span class="pill st-cancel">ยกเลิก</span>
                            @elseif($item->statuspdf == 3)
                                <span class="pill st-noprint">ไม่ปริ้นบิล</span>
                            @else
                                <button type="button" class="pill st-done @if(!empty($item->has_delivery)) has-deli @endif"
                                      title="{{ !empty($item->has_delivery) ? 'มีการจัดส่งแล้ว — ดูข้อมูล' : 'ดูข้อมูลจัดส่ง / รับเข้า' }}"
                                      onclick="openDeliveryPopup({{ json_encode((string) $item->billid) }})">@if(!empty($item->has_delivery))<svg class="i" viewBox="0 0 24 24"><path d="M1 4h14v12H1zM15 8h4l3 3v5h-7z"/><circle cx="5.5" cy="18.5" r="2"/><circle cx="18.5" cy="18.5" r="2"/></svg>@endif ปริ้นสำเร็จ</button>
                            @endif
                            @if($item->print_time)
                                <span class="sub">{{ \Carbon\Carbon::parse($item->print_time)->format('H:i d/m/Y') }}</span>
                            @endif
                            </td>

                            <td class="c-more" data-label="ข้อมูลสินค้า">
                                <a class="btn-more" href="javascript:void(0);"
                                onclick="openPopup(
                                    {{ json_encode($item->so_detail_id) }},
                                    {{ json_encode($item->so_id) }},
                                    {{ json_encode($item->ponum) }},
                                    {{ json_encode($item->customer_name) }},
                                    {{ json_encode($item->customer_address) }},
                                    {{ json_encode(\Carbon\Carbon::parse($item->date_of_dali)->format('d/m/Y')) }},
                                    {{ json_encode($item->sale_name) }},
                                    {{ json_encode($item->POdocument) }},
                                    {{ json_encode($item->notes) }}
                                )"><svg class="i" viewBox="0 0 24 24"><path d="M21 8l-9-5-9 5v8l9 5 9-5z"/><path d="M3 8l9 5 9-5M12 13v8"/></svg>เพิ่มเติม</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @if(isset($message))
                <div class="empty"><svg class="i" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg><div>{{ $message }}</div></div>
            @elseif($bill->isEmpty())
                <div class="empty"><svg class="i" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg><div>ไม่พบข้อมูลตามตัวกรอง</div></div>
            @endif
        </div>

        <div class="pagination-wrap">
            {{ $bill->appends(request()->query())->links() }}
        </div>
    </main>

    <!-- Popup ข้อมูลจัดส่ง / รับเข้า (กดจากสถานะ "ปริ้นสำเร็จ") -->
    <div class="popup-overlay" id="deliveryPopup" style="display: none;">
        <div class="popup-content" style="max-width:680px;">
            <div class="pp-head">
                <span class="ic"><svg class="i" viewBox="0 0 24 24"><path d="M1 4h14v12H1zM15 8h4l3 3v5h-7z"/><circle cx="5.5" cy="18.5" r="2"/><circle cx="18.5" cy="18.5" r="2"/></svg></span>
                <div><h3>ข้อมูลจัดส่ง / รับเข้า</h3><span class="sub">บิล <b id="dpBill"></b></span></div>
                <button type="button" class="close-btn" onclick="closeDeliveryPopup()" aria-label="ปิด"><svg class="i" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
            </div>
            <div class="pp-body" id="dpBody"></div>
        </div>
    </div>
    <script>
    const BILL_DELIVERY_URL = "{{ route('sale.billDelivery') }}";
    function dpEsc(s){ return (s==null?'':String(s)).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }
    /* ===== ไทม์ไลน์รายละเอียดการจัดส่ง (โค้ดชุดเดียวกันในหน้า sale/dashboard, document/dashboarddoc, so/show) =====
       rows = transaction_transport ทุกรอบของบิล (รวมรอบประวัติ cancelled_at) เรียงเก่า -> ใหม่
       แต่ละรอบบอก: ใครจ่ายงาน -> ใครไปส่ง/รถอะไร/วันไหน -> ผลเป็นอย่างไร ใครยืนยัน -> จบรอบเพราะอะไร (ส่งใหม่/เปลี่ยนคนขับ/ยกเลิก) */
    function dlvEsc(x){ return (x==null?'':String(x)).replace(/[&<>"']/g,function(c){return({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]);}); }
    // สี/ข้อความตามผล (ชุดเดียวกับหน้า billreceive): สำเร็จ=เขียว, สินค้าผิด=แดง, ส่งใหม่=ฟ้า, ค้างบิล=ส้ม
    function dlvStyle(st){
        st = (st||'').toString().trim();
        if(st.indexOf('สำเร็จ')!==-1 && st.indexOf('ไม่')===-1) return {bg:'#e8f5e9',fg:'#1b5e20',border:'#2e7d32',txt:'สำเร็จ'};
        if(st.indexOf('สินค้าผิด')!==-1) return {bg:'#ffebee',fg:'#a91f1f',border:'#c62828',txt:'สินค้าผิด'};
        if(st.indexOf('ไม่สำเร็จ')!==-1) return {bg:'#ffebee',fg:'#a91f1f',border:'#c62828',txt:'ไม่สำเร็จ'};
        if(st.indexOf('ส่งใหม่')!==-1) return {bg:'#eaf0fc',fg:'#2853d5',border:'#2853d5',txt:'ส่งใหม่'};
        if(st.indexOf('ค้างบิล')!==-1) return {bg:'#fff4e5',fg:'#b45309',border:'#ed6c02',txt:'ค้างบิล'};
        if(st===''||st==='0') return {bg:'#f3f4f6',fg:'#6b7280',border:'#d1d5db',txt:'กำลังไปส่ง'};
        return {bg:'#f3f4f6',fg:'#374151',border:'#d1d5db',txt:st};
    }
    function dlvBadge(txt, s){ return '<span style="background:'+s.bg+';color:'+s.fg+';font-size:12px;font-weight:700;padding:2px 10px;border-radius:10px;white-space:nowrap;">'+dlvEsc(txt)+'</span>'; }
    // note อัตโนมัติตอนกดส่งใหม่ ("ส่งใหม่ (ไม่สำเร็จ) เหตุผล: .. · เคยไปวันที่ .. · จ่ายใหม่ให้ ..") -> ดึงเหตุผล/ผู้รับงานใหม่ออกมา
    //   ส่วนอื่นซ้ำกับข้อมูลที่แสดงอยู่แล้วจึงไม่แสดงซ้ำ ; note ที่คนพิมพ์เอง (ค้างบิล/สินค้าผิด/ของผิด) แสดงตามจริง
    function dlvParseNote(note){
        note = (note||'').toString().trim();
        if(!note) return {text:''};
        if(note.indexOf('ส่งใหม่ (ไม่สำเร็จ)') === 0){
            var m1 = note.match(/เหตุผล:\s*([^·]+)/), m2 = note.match(/จ่ายใหม่ให้\s*([^·]+?)(?:\s*·|$)/);
            return {auto:true, reason: m1 ? m1[1].trim() : '', reassign: m2 ? m2[1].trim() : '', text:''};
        }
        return {text: note};
    }
    function dlvLine(label, val){
        return '<div style="display:flex;gap:8px;align-items:baseline;">'
             + '<span style="flex:0 0 110px;color:#6b7280;white-space:nowrap;">'+label+'</span>'
             + '<span style="flex:1;min-width:0;">'+val+'</span></div>';
    }
    function dlvTimeline(rows){
        rows = rows || [];
        var total = rows.length, redoN = 0, active = [];
        rows.forEach(function(r){
            if((r.status||'').indexOf('ส่งใหม่') !== -1) redoN++;
            if(!r.cancelled_at) active.push(r);
        });
        var latest = active.length ? active[active.length-1] : null;

        // ── สรุปด้านบน ──
        var h = '<div style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:10px;padding:10px 14px;margin-bottom:12px;font-size:13.5px;color:#374151;line-height:1.8;">'
              + 'จ่ายงานไปส่งทั้งหมด <b>'+total+'</b> รอบ' + (redoN ? ' · สั่งส่งใหม่ <b>'+redoN+'</b> ครั้ง' : '') + '<br>'
              + 'สถานะตอนนี้: ';
        if(latest){
            var ls = dlvStyle(latest.status);
            h += dlvBadge(ls.txt, ls) + ' · คนขับ <b>'+dlvEsc(latest.driver_name||'-')+'</b>'
               + (latest.transport_name ? ' ('+dlvEsc(latest.transport_name)+')' : '')
               + (latest.delivery_date ? ' · ไปส่งวันที่ <b>'+dlvEsc(latest.delivery_date)+'</b>' : '');
        } else {
            h += '<b style="color:#9a3412;">รอจ่ายงานใหม่</b> (ทุกรอบถูกส่งใหม่/ยกเลิกแล้ว — รอจ่ายที่หน้าจ่ายงานขนส่ง)';
        }
        h += '</div>';

        // ── ทีละรอบ ──
        rows.forEach(function(r, i){
            var isHist = !!r.cancelled_at;
            var isRedo = (r.status||'').indexOf('ส่งใหม่') !== -1;
            var next   = rows[i+1];
            // รอบที่จบไปแล้ว: ส่งใหม่ / เปลี่ยนคนขับ (รอบถัดไปเป็น note "เปลี่ยน...") / ยกเลิกการจ่ายงาน (คืนคิว)
            var kind = !isHist ? '' : (isRedo ? 'redo' : ((next && /^เปลี่ยน/.test((next.note||'').trim())) ? 'change' : 'cancel'));
            var s    = dlvStyle(r.status);
            var pn   = dlvParseNote(r.note);
            var st   = (r.status||'').toString().trim();
            var hasResult = st !== '' && st !== '0' && !isRedo;   // มีผลจริง (สำเร็จ/ค้างบิล/สินค้าผิด)

            var head, border;
            if(kind === 'redo')        { head = dlvBadge('ไม่สำเร็จ / ส่งใหม่', dlvStyle('ส่งใหม่')); border = '#2853d5'; }
            else if(kind === 'change') { head = dlvBadge('เปลี่ยนคนขับ/ขนส่ง', dlvStyle('ส่งใหม่')); border = '#2853d5'; }
            else if(kind === 'cancel') { head = dlvBadge('ยกเลิกการจ่ายงาน', {bg:'#f3f4f6',fg:'#6b7280'}); border = '#9ca3af'; }
            else                       { head = dlvBadge(s.txt, s); border = s.border; }

            h += '<div style="border:2px '+(isHist?'dashed':'solid')+' '+border+';border-radius:10px;padding:10px 14px;background:#fff;">'
               + '<div style="display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:6px;">'
               +   '<span style="font-weight:700;color:#111827;">รอบที่ '+(i+1)
               +   (isHist ? ' <span style="color:#9a3412;font-size:12px;font-weight:600;">(จบรอบแล้ว)</span>'
                           : (r === latest ? ' <span style="color:#1b5e20;font-size:12px;font-weight:600;">(รอบปัจจุบัน)</span>' : ''))
               +   '</span>' + head
               + '</div>'
               + '<div style="font-size:13.5px;color:#374151;line-height:1.9;">';

            // 1) จ่ายงาน
            h += dlvLine('จ่ายงานโดย', '<b>'+dlvEsc(r.name_pick||'-')+'</b>' + (r.time_pick ? ' · '+dlvEsc(r.time_pick) : ''));
            // 2) ใครไปส่ง รถอะไร วันไหน
            h += dlvLine('ผู้ไปส่ง', '<b>'+dlvEsc(r.driver_name||'-')+'</b>'
                 + (r.transport_name ? ' · '+dlvEsc(r.transport_name) : '')
                 + (r.delivery_date ? ' · ให้ไปส่งวันที่ <b>'+dlvEsc(r.delivery_date)+'</b>' : '')
                 + (r.id_transport ? ' · เลขขนส่ง <b>'+dlvEsc(r.id_transport)+'</b>' : ''));
            // 3) ผลการไปส่ง
            if(hasResult){
                h += dlvLine('ผลการส่ง', dlvBadge(s.txt, s)
                     + ' · ยืนยันโดย <b>'+dlvEsc(r.check_name||'-')+'</b>' + (r.check_time ? ' · '+dlvEsc(r.check_time) : ''));
            } else if(kind === 'redo'){
                h += dlvLine('ผลการส่ง', '<b style="color:#a91f1f;">ไม่สำเร็จ</b>');
            } else if(!isHist){
                h += dlvLine('ผลการส่ง', '<span style="color:#9ca3af;">ยังไม่ยืนยันผล (อยู่ระหว่างไปส่ง)</span>');
            }
            // 4) จบรอบเพราะอะไร ใครทำ เมื่อไหร่
            var by = '<b>'+dlvEsc(r.cancelled_by || r.check_name || '-')+'</b>' + (r.cancelled_at ? ' · '+dlvEsc(r.cancelled_at) : '');
            if(kind === 'redo'){
                h += dlvLine('สั่งส่งใหม่', by
                     + (pn.reason ? ' · เหตุผล <b style="color:#a91f1f;">'+dlvEsc(pn.reason)+'</b>' : '')
                     + (pn.reassign ? ' · จ่ายใหม่ให้ '+dlvEsc(pn.reassign) : ''));
            } else if(kind === 'change'){
                h += dlvLine('เปลี่ยนคนขับ', by + ' · ไปต่อที่รอบที่ '+(i+2));
            } else if(kind === 'cancel'){
                h += dlvLine('ยกเลิกการจ่าย', by + ' · คืนงานไปหน้าจ่ายงาน');
            }
            // 5) หมายเหตุที่คนพิมพ์ (ค้างบิล/สินค้าผิด/ของผิด/เปลี่ยนคนขับ ฯลฯ)
            if(pn.text){ h += dlvLine('หมายเหตุ', '<span style="color:#6b7280;">'+dlvEsc(pn.text)+'</span>'); }

            h += '</div></div>';
            if(i < total-1){ h += '<div style="text-align:center;color:#9ca3af;font-size:11px;line-height:1;padding:3px 0;color:#c7ccd3;">|</div>'; }
        });
        return h;
    }
    async function openDeliveryPopup(billid){
        const pop=document.getElementById('deliveryPopup');
        document.getElementById('dpBill').textContent=billid;
        document.getElementById('dpBody').innerHTML='<div style="padding:16px;text-align:center;color:#6b7280;">กำลังโหลด...</div>';
        pop.style.display='flex';
        try{
            const res=await fetch(BILL_DELIVERY_URL+'?billid='+encodeURIComponent(billid),{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
            const data=await res.json();
            const rows=(data&&data.rows)||[];
            if(!rows.length){ document.getElementById('dpBody').innerHTML='<div style="padding:16px;color:#6b7280;text-align:center;">ยังไม่มีข้อมูลการจ่ายงาน/จัดส่งของบิลนี้</div>'; return; }
            document.getElementById('dpBody').innerHTML=dlvTimeline(rows);
        }catch(e){ document.getElementById('dpBody').innerHTML='<div style="padding:16px;color:#dc2626;text-align:center;">โหลดข้อมูลไม่สำเร็จ</div>'; }
    }
    function closeDeliveryPopup(){ document.getElementById('deliveryPopup').style.display='none'; }
    document.getElementById('deliveryPopup').addEventListener('click',function(e){ if(e.target===this) closeDeliveryPopup(); });
    </script>

    <!-- Popup ข้อมูลสินค้า -->
    <div class="popup-overlay" id="popup" style="display: none;">
        <div class="popup-content">
            <div class="pp-head">
                <span class="ic"><svg class="i" viewBox="0 0 24 24"><path d="M21 8l-9-5-9 5v8l9 5 9-5z"/><path d="M3 8l9 5 9-5M12 13v8"/></svg></span>
                <div><h3>ข้อมูลสินค้า</h3><span class="sub" id="popup-sub"></span></div>
                <button type="button" class="close-btn" onclick="closePopup()" aria-label="ปิด"><svg class="i" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
            </div>
            <div class="pp-body table-container">
                <div class="pp-sec"><svg class="i" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8"/></svg>ข้อมูลใบสั่งขาย</div>
                <table class="pp-table">
                    <thead>
                        <tr>
                            <th>REF</th>
                            <th>ชื่อลูกค้า</th>
                            <th>ที่อยู่จัดส่ง</th>
                            <th style="white-space:nowrap;">วันที่จัดส่ง</th>
                            <th style="white-space:nowrap;">ผู้เปิด</th>
                            <th style="white-space:nowrap;">เอกสาร PO</th>
                        </tr>
                    </thead>
                    <tbody id="popup-body-1"></tbody>
                </table>
                <div class="pp-sec"><svg class="i" viewBox="0 0 24 24"><path d="M21 8l-9-5-9 5v8l9 5 9-5z"/><path d="M3 8l9 5 9-5M12 13v8"/></svg>รายการสินค้า</div>
                <table class="pp-table">
                    <thead>
                        <tr>
                            <th>รหัสสินค้า</th>
                            <th>รายการ</th>
                            <th class="r">จำนวน</th>
                            <th class="r">ราคาต่อหน่วย</th>
                        </tr>
                    </thead>
                    <tbody id="popup-body"></tbody>
                </table>
                <div class="pp-sec"><svg class="i" viewBox="0 0 24 24"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1z"/><path d="M8 8h8M8 12h8M8 16h5"/></svg>หมายเหตุ</div>
                <textarea id="popup-body-3" readonly></textarea>
            </div>
        </div>
    </div>

    <div id="uiToast" class="ui-toast" role="status" aria-live="polite"></div>
    <script>
    // แจ้งเตือนเล็ก (แทน alert ของเบราว์เซอร์)
    let uiToastTimer = null;
    function uiToast(text, tone){
        const t = document.getElementById('uiToast');
        t.className = 'ui-toast' + (tone === 'error' ? ' t-error' : '');
        t.textContent = text;
        requestAnimationFrame(() => t.classList.add('show'));
        clearTimeout(uiToastTimer); uiToastTimer = setTimeout(() => t.classList.remove('show'), 3200);
    }
    </script>

{{-- ✅ ย้าย merge script ออกมานอก loop (เดิมถูกประกาศซ้ำทุกแถว) --}}
<script>
    function mergeAndOpenPdfs(billid) {
        const pdfDocUrl = "{{ asset('storage/doc_document') }}/" + billid + ".pdf";
        const win1 = window.open(pdfDocUrl, "_blank");
        fetch("{{ route('merge.pdf') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ billid: billid })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && win1) {
                setTimeout(() => {
                    win1.location.reload();
                }, 800);
            } else {
                uiToast(data.message || "เกิดข้อผิดพลาดในการ merge PDF", "error");
            }
        })
        return false;
    }

    function openBillOnly(billid) {
        const pdfBillUrl = "{{ asset('storage/bill_document') }}/" + billid + ".pdf";
        const win1 = window.open(pdfBillUrl, "_blank");

        fetch("{{ route('merge.pdf') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ billid: billid })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && win1) {
                setTimeout(() => {
                    win1.location.reload();
                }, 800);
            } else {
                uiToast(data.message || "เกิดข้อผิดพลาดในการ merge PDF", "error");
            }
        })
        return false;
    }
</script>

    <script>
    function openPopup(soDetailId,so_id,ponum,customer_name,customer_address,date_of_dali,sale_name,POdocument,notes) {
        document.getElementById("popup").style.display = "flex";
        document.getElementById("popup-sub").textContent = 'SO ' + (so_id || '-') + (ponum ? ' · PO ' + ponum : '');

        let popupBody = document.getElementById("popup-body-1");
        popupBody.innerHTML = `
            <tr>
                <td class="num">${dpEsc(soDetailId)}</td>
                <td>${dpEsc(customer_name)}</td>
                <td>${dpEsc(customer_address)}</td>
                <td class="num">${dpEsc(date_of_dali)}</td>
                <td>${dpEsc(sale_name)}</td>
                <td>${POdocument ? `<a href="/storage/po_documents/${encodeURIComponent(POdocument)}" target="_blank">ดูไฟล์</a>` : '<span style="color:#94a3b8">-</span>'}</td>
            </tr>
        `;
        document.getElementById("popup-body-3").value = notes || '';
        let secondPopupBody = document.getElementById("popup-body");
        secondPopupBody.innerHTML = "<tr><td colspan='4' style='text-align:center;color:#64748b;padding:18px'>กำลังโหลด...</td></tr>";

        fetch(`/get-bill-detail/${soDetailId}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    secondPopupBody.innerHTML = "";
                    data.forEach(item => {
                        secondPopupBody.insertAdjacentHTML("beforeend", `
                            <tr>
                                <td class="num">${dpEsc(item.item_id)}</td>
                                <td>${dpEsc(item.item_name)}</td>
                                <td class="r num"><b>${dpEsc(item.quantity)}</b></td>
                                <td class="r num">${dpEsc(item.unit_price)}</td>
                            </tr>
                        `);
                    });
                } else {
                    secondPopupBody.innerHTML = "<tr><td colspan='4' style='text-align:center;color:#64748b;padding:18px'>ไม่มีข้อมูล</td></tr>";
                }
            })
            .catch(error => {
                console.error("Error fetching data:", error);
                secondPopupBody.innerHTML = "<tr><td colspan='4' style='text-align:center;color:#dc2626;padding:18px'>เกิดข้อผิดพลาด</td></tr>";
            });
    }

    function closePopup() {
        document.getElementById('popup').style.display = 'none';
    }

    window.onclick = function(event) {
        var popup = document.getElementById('popup');
        if (event.target === popup) {
            closePopup();
        }
    }
    // กด Esc ปิดป๊อปอัพ
    document.addEventListener('keydown', function(e){ if (e.key === 'Escape') { closePopup(); closeDeliveryPopup(); } });
    </script>

<script>
  function openPopup3E(soId) {
    const url = `http://server-3e/3e/store_report.php?so=${encodeURIComponent(soId)}&po=&search=Search&rowPerPage=25&currentPage=0`;

    const w = 1100, h = 500;
    const margin = 16;

    const dualScreenLeft = window.screenLeft ?? window.screenX ?? 0;
    const dualScreenTop  = window.screenTop  ?? window.screenY ?? 0;

    const width  = window.innerWidth  ?? document.documentElement.clientWidth  ?? screen.width;
    const height = window.innerHeight ?? document.documentElement.clientHeight ?? screen.height;

    const left = Math.max(dualScreenLeft + width  - w - margin, dualScreenLeft);
    const top  = Math.max(dualScreenTop  + height - h - margin, dualScreenTop);

    const features = [
      'toolbar=no',
      'location=no',
      'status=no',
      'menubar=no',
      'scrollbars=yes',
      'resizable=yes',
      `width=${w}`,
      `height=${h}`,
      `top=${top}`,
      `left=${left}`,
      'noopener=yes'
    ].join(',');

    const win = window.open(url, '_blank', features);

    if (!win) window.open(url, '_blank', 'noopener');

    return false;
  }
</script>
<script>
function mergePdf(billid) {
    fetch("{{ route('merge.pdf') }}", {
        method: "POST",
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ billid: billid })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            uiToast(data.message || "เกิดข้อผิดพลาด", "error");
        }
    })
    .catch(err => {
        uiToast("เกิดข้อผิดพลาด: " + err.message, "error");
        console.error(err);
    });
}
</script>

</body>
</html>