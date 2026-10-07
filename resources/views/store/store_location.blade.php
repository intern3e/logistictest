<!DOCTYPE html>
{{-- resources/views/store/store_location.blade.php  (ด่าน 2: ระบุตำแหน่ง) — ธีมเดียวกับหน้า ชั้น SALE --}}
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ระบุตำแหน่งจัดเก็บ</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root{
            --ink:#0f172a; --ink-700:#334155; --muted:#64748b; --faint:#94a3b8;
            --page-bg:#f8fafc; --soft:#f1f5f9; --border:#e2e8f0; --line:#e8edf3;
            --primary:#1a4fd6; --primary-dark:#123bb0; --primary-light:#eaf0fd;
            --success:#16a34a; --success-dark:#166534; --success-light:#dcfce7;
            --danger:#dc2626; --danger-dark:#b91c1c; --danger-light:#fee2e2;
            --warning:#d97706; --warning-dark:#b45309; --warning-light:#fef3c7;
            --shadow:0 1px 3px rgba(15,23,42,.04),0 4px 12px rgba(15,23,42,.05);
            --r-card:12px; --r-field:8px;
        }
        *{box-sizing:border-box;margin:0;padding:0}
        html{font-size:clamp(14.5px,0.3vw + 10.5px,17px)}
        html,body{background:var(--page-bg);font-family:'Sarabun','Segoe UI',Tahoma,sans-serif;color:var(--ink);line-height:1.5;-webkit-font-smoothing:antialiased}
        body{min-height:100vh;overflow-x:hidden}
        button,select,input{font:inherit}
        button{cursor:pointer}
        svg.i{width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;flex:none}
        .page-frame{width:100%;min-height:100vh;display:flex;flex-direction:column}

        /* ===== แถบบน (แบบเดียวกับหน้า ชั้น SALE) ===== */
        .top-banner{display:flex;align-items:center;gap:14px;flex-wrap:wrap;background:#fff;border:1px solid var(--line);border-radius:var(--r-card);
                    margin:15px 15px 12px;padding:12px 16px 12px 18px;box-shadow:var(--shadow);position:sticky;top:10px;z-index:100}
        .top-banner .h1{flex:none;display:flex;align-items:center;gap:10px;font-weight:800;font-size:clamp(19px,.8vw + 10px,23px);letter-spacing:-.3px;white-space:nowrap;color:var(--ink);text-decoration:none}
        .top-banner .logo{width:38px;height:38px;border-radius:10px;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 10px rgba(26,79,214,.28)}
        .top-banner .logo svg{width:19px;height:19px}
        .vsep{width:1px;height:28px;background:var(--line);flex:none}
        .tools{flex:1 1 640px;display:flex;align-items:center;gap:8px;min-width:0}
        .banner-right{display:flex;align-items:center;gap:8px;margin-left:auto;flex-shrink:0}
        .fld{height:40px;display:flex;align-items:center;gap:8px;padding:0 12px;background:var(--page-bg);border:1px solid var(--line);border-radius:var(--r-field);transition:.15s;min-width:0;flex:1 1 180px;position:relative}
        .fld:hover{border-color:var(--border)}
        .fld:focus-within{background:#fff;border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light)}
        .fld > svg{color:var(--faint)}
        .fld input{border:0;outline:0;background:transparent;font-size:.95rem;color:var(--ink);height:100%;min-width:0;padding:0;flex:1;width:100%}
        .fld input::placeholder{color:var(--faint)}
        .btn-icon{width:40px;height:40px;flex:none;display:inline-flex;align-items:center;justify-content:center;border:1px solid var(--line);border-radius:var(--r-field);background:#fff;color:var(--muted);text-decoration:none;transition:.15s}
        .btn-icon svg{width:17px;height:17px}
        .btn-icon:hover{background:var(--soft);color:var(--ink);border-color:var(--border)}
        .btn-icon.reset:hover{color:var(--danger);border-color:#fecaca;background:#fef2f2}

        /* รอระบุตำแหน่ง (ป้ายแจ้งงานค้าง) */
        @keyframes alertPulse{0%,100%{box-shadow:0 0 0 0 rgba(217,119,6,.35)}50%{box-shadow:0 0 0 6px rgba(217,119,6,0)}}
        .todo-alert{height:40px;display:inline-flex;align-items:center;gap:9px;padding:0 14px 0 8px;border:1px solid #fde68a;background:linear-gradient(180deg,#fffbeb,#fef3c7);color:var(--warning-dark);
                    border-radius:var(--r-field);font-size:.95rem;font-weight:700;white-space:nowrap;animation:alertPulse 1.8s ease-in-out infinite}
        .todo-alert .oa-icon{width:24px;height:24px;border-radius:50%;background:var(--warning);color:#fff;display:inline-flex;align-items:center;justify-content:center;box-shadow:0 2px 6px rgba(217,119,6,.35)}
        .todo-alert .oa-icon svg{width:14px;height:14px;stroke-width:2.4}
        .todo-alert b{font-size:1.1rem;font-weight:800;font-variant-numeric:tabular-nums}
        .todo-alert.zero{animation:none;background:var(--success-light);border-color:#bbf7d0;color:var(--success-dark)}
        .todo-alert.zero .oa-icon{background:var(--success);box-shadow:none}
        .user-badge{height:40px;display:inline-flex;align-items:center;gap:8px;padding:0 12px 0 5px;border-radius:999px;background:var(--page-bg);border:1px solid var(--line);font-size:.86rem;color:var(--ink-700);white-space:nowrap}
        .user-badge .av{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:var(--soft);color:var(--ink)}
        .user-badge .av svg{width:15px;height:15px}

        /* ===== ปุ่มค้นหา (แบบเดียวกับหน้า ชั้น SALE) ===== */
        .search-btn{height:40px;flex:none;display:inline-flex;align-items:center;gap:8px;padding:0 12px 0 16px;border:0;border-radius:var(--r-field);background:var(--primary);color:#fff;
                    font-size:1.02rem;font-weight:700;white-space:nowrap;box-shadow:0 1px 0 rgba(255,255,255,.22) inset,0 3px 10px rgba(26,79,214,.20);transition:transform .12s,background .15s}
        .search-btn:hover{background:var(--primary-dark);transform:translateY(-1px)}
        .search-btn:active{transform:none}
        .search-btn .sb-ic{display:inline-flex}
        .search-btn .sb-ic svg{width:19px;height:19px}
        .search-btn .sb-kbd{font:inherit;font-size:.76rem;font-weight:600;color:rgba(255,255,255,.9);background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.28);border-radius:6px;padding:1px 6px;line-height:18px}
        .search-btn .sb-spin{display:none;width:16px;height:16px;border-radius:50%;border:2px solid rgba(255,255,255,.35);border-top-color:#fff;animation:spin .7s linear infinite}
        .search-btn.loading .sb-ic{display:none}
        .search-btn.loading .sb-spin{display:inline-block}
        /* ===== ดรอปดาวน์ (แบบเดียวกับดรอปดาวน์สถานะหน้า ชั้น SALE) — select เดิมซ่อนอยู่ข้างใน ===== */
        .dd{flex:0 0 200px;cursor:pointer;user-select:none}
        .dd .dd-native{position:absolute;width:1px;height:1px;opacity:0;pointer-events:none;left:0;bottom:0}
        .dd .dd-btn{flex:1;min-width:0;height:100%;border:0;background:transparent;padding:0;text-align:left;font-size:.95rem;font-weight:600;color:var(--ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;outline:0}
        .dd .caret{display:flex;color:var(--muted)}
        .dd .caret svg{width:17px;height:17px;transition:transform .15s}
        .dd.open{background:#fff;border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light)}
        .dd.open .caret svg{transform:rotate(180deg)}
        .dd .st-dot{width:10px;height:10px;border-radius:50%;flex:none;background:var(--faint)}
        .dd-panel{display:none;position:absolute;left:0;top:calc(100% + 6px);min-width:100%;z-index:300;background:#fff;border:1px solid var(--border);border-radius:12px;box-shadow:0 12px 32px rgba(15,23,42,.16);padding:6px;cursor:default}
        .dd.open .dd-panel{display:block}
        .dd-item{display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:8px;font-size:.95rem;color:var(--ink);cursor:pointer;white-space:nowrap}
        .dd-item:hover,.dd-item.hl{background:var(--soft)}
        .dd-item .d{width:9px;height:9px;border-radius:50%;flex:none}
        .dd-item .n{margin-left:auto;padding-left:16px;font-weight:700;color:var(--muted);font-variant-numeric:tabular-nums}
        .dd-item .ck{width:15px;height:15px;visibility:hidden;color:var(--ink)}
        .dd-item.sel{font-weight:700}
        .dd-item.sel .ck{visibility:visible}
        .dd-item.sel .n{color:var(--ink)}

        main{padding:0 15px 40px;flex:1;width:100%}

        /* แถบข้อมูลเหนือตาราง */
        .table-topbar{display:flex;align-items:center;gap:10px 14px;flex-wrap:wrap;margin:0 0 12px;color:var(--muted);font-size:.9rem}
        .table-topbar b{color:var(--ink);font-variant-numeric:tabular-nums}
        .table-topbar .tip{margin-left:auto;display:inline-flex;align-items:center;gap:6px;color:var(--faint);font-size:.84rem}
        .table-topbar .tip svg{width:14px;height:14px}

        /* ===== ตาราง: หัวตารางสีฟ้า + เส้นตาราง ===== */
        .table-scroll{background:#fff;border:1px solid var(--line);border-radius:var(--r-card);overflow:hidden;box-shadow:var(--shadow)}
        .table-inner{overflow-x:auto;-webkit-overflow-scrolling:touch;width:100%}
        table{width:100%;min-width:760px;border-collapse:collapse;background:#fff}
        th,td{border-bottom:1px solid var(--border);border-right:1px solid var(--line);padding:11px 14px;text-align:center;font-size:.92rem;vertical-align:middle}
        th:last-child,td:last-child{border-right:0}
        thead th{background:var(--primary);color:#fff;font-weight:700;font-size:.84rem;border-right-color:rgba(255,255,255,.18);border-bottom:0;white-space:nowrap}
        tbody tr{transition:background .12s}
        tbody tr:nth-child(even){background:#fafbfd}
        tbody tr:hover{background:#eef3fe}
        tbody tr:last-child td{border-bottom:0}
        tbody tr.hidden-row{display:none}
        tbody tr:has(.chkLine:checked){background:#e3ebfd}
        tbody tr.done td{color:var(--muted)}
        input[type=checkbox]{width:18px;height:18px;accent-color:var(--primary);cursor:pointer;vertical-align:middle}
        thead input[type=checkbox]{accent-color:#fff}
        td.c-chk{width:52px}
        .cust-cell{text-align:left}
        .cust-cell .nm{font-weight:600;color:var(--ink);max-width:300px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;line-height:1.4}
        .sub{font-size:.8rem;color:var(--faint);margin-top:2px}
        .dash{color:var(--faint)}
        .ref-link{font-weight:700;color:var(--ink);white-space:nowrap}
        a.so-link{font-weight:700;color:var(--primary);text-decoration:none;white-space:nowrap}
        a.so-link:hover{text-decoration:underline}
        .po-cell{display:flex;flex-direction:column;align-items:center;gap:5px}
        .po-type-badge{display:inline-flex;align-items:center;gap:5px;padding:1px 9px;border-radius:999px;font-size:.74rem;font-weight:700;white-space:nowrap}
        .po-type-badge::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
        .po-internal{background:var(--primary-light);color:var(--primary)}
        .po-external{background:var(--warning-light);color:var(--warning-dark)}
        .vendor-int{color:var(--faint);font-size:.88rem}
        .btn-view-items{height:28px;display:inline-flex;align-items:center;gap:5px;padding:0 10px;border:1px solid var(--line);border-radius:7px;background:#fff;color:var(--ink-700);font-size:.8rem;font-weight:600;white-space:nowrap;transition:.15s}
        .btn-view-items svg{width:13px;height:13px}
        .btn-view-items:hover{border-color:#c7d6fb;background:var(--primary-light);color:var(--primary)}

        /* คอลัมน์จัดการ */
        .manage{display:flex;flex-direction:column;align-items:center;gap:3px}
        .muted{font-size:.8rem;color:var(--muted);white-space:nowrap}
        .btn-finish-claim{height:34px;display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:0 14px;border:1px solid #bbf7d0;border-radius:var(--r-field);background:var(--success-light);color:var(--success-dark);font-weight:700;font-size:.86rem;white-space:nowrap;
                          transition:.15s;margin-bottom:2px}
        .btn-finish-claim svg{width:15px;height:15px;stroke-width:2.5}
        .btn-finish-claim:hover{background:var(--success);border-color:var(--success);color:#fff}
        .btn-finish-claim:disabled{opacity:.6;cursor:progress}
        .finished-tag{display:inline-flex;align-items:center;gap:6px;padding:3px 11px;border-radius:999px;background:var(--success-light);color:var(--success-dark);font-size:.8rem;font-weight:700;white-space:nowrap;margin-bottom:2px}
        .finished-tag::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
        .claim-select{height:34px;min-width:150px;padding:0 32px 0 12px;border:1px dashed #c7d6fb;border-radius:var(--r-field);background:var(--primary-light) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%231a4fd6' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") no-repeat right 10px center/15px;
                      color:var(--primary);font-weight:700;font-size:.86rem;appearance:none;-webkit-appearance:none;cursor:pointer;outline:0;transition:.15s}
        .claim-select:hover{border-style:solid;border-color:var(--primary)}
        .claim-select:focus{border-style:solid;border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light);background-color:#fff}
        .claim-select:disabled{opacity:.6;cursor:progress}
        .loc-tag{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:7px;background:var(--soft);color:var(--ink);font-weight:700;font-size:.86rem;white-space:nowrap}
        .loc-tag svg{width:13px;height:13px;color:var(--muted)}
        .packer{font-weight:600;color:var(--ink-700);white-space:nowrap}
        td.c-time{white-space:nowrap;font-variant-numeric:tabular-nums;color:var(--muted);font-size:.82rem}
        td.empty{padding:70px 20px;color:var(--muted);font-size:.95rem;text-align:center}
        td.empty svg{display:block;margin:0 auto 10px;width:40px;height:40px;color:var(--border);stroke-width:1.5}
        tbody tr:has(td.empty):hover{background:#fff}

        /* ===== แบ่งหน้า ===== */
        .pagination{display:flex;align-items:center;justify-content:center;gap:10px;margin-top:16px;flex-wrap:wrap}
        .page-btn{height:38px;display:inline-flex;align-items:center;padding:0 16px;border:1px solid var(--line);border-radius:999px;background:#fff;color:var(--ink-700);font-weight:600;font-size:.9rem;text-decoration:none;box-shadow:var(--shadow);transition:.15s}
        a.page-btn:hover{border-color:var(--primary);color:var(--primary)}
        .page-btn.disabled{color:var(--faint);box-shadow:none;background:var(--page-bg)}
        .page-info{color:var(--muted);font-size:.9rem}

        /* ===== ปุ่มลอย "ระบุตำแหน่ง" (โผล่เมื่อเลือกรายการ) ===== */
        @keyframes floatIn{from{opacity:0;transform:translate(-50%,16px)}to{opacity:1;transform:translate(-50%,0)}}
        .float-action{position:fixed;left:50%;bottom:22px;transform:translateX(-50%);z-index:150;height:52px;display:inline-flex;align-items:center;gap:10px;padding:0 10px 0 20px;border:0;border-radius:999px;
                      background:var(--primary);color:#fff;font-size:1.05rem;font-weight:700;white-space:nowrap;box-shadow:0 10px 30px rgba(26,79,214,.38),0 1px 0 rgba(255,255,255,.22) inset;animation:floatIn .18s ease-out}
        .float-action[hidden]{display:none}
        .float-action:hover{background:var(--primary-dark)}
        .float-action svg{width:19px;height:19px}
        .float-action .fa-cnt{display:inline-flex;align-items:center;gap:4px;height:34px;padding:0 13px;border-radius:999px;background:#fff;color:var(--primary);font-size:.92rem;font-weight:800;font-variant-numeric:tabular-nums}

        /* ===== ป๊อปอัพ (dialog) ===== */
        dialog{border:0;border-radius:16px;padding:0;width:min(94vw,480px);max-height:88vh;margin:auto;box-shadow:0 24px 60px rgba(0,0,0,.28);color:var(--ink);overflow:visible}
        dialog::backdrop{background:rgba(15,23,42,.55)}
        dialog[open]{animation:modalPop .15s ease-out}
        @keyframes modalPop{from{opacity:0;transform:translateY(8px) scale(.98)}to{opacity:1;transform:none}}
        .dialog-header{display:flex;align-items:center;gap:12px;padding:20px 22px 0}
        .dialog-header .d-icon{width:42px;height:42px;border-radius:10px;flex:none;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center}
        .dialog-header .d-icon svg{width:20px;height:20px}
        .dialog-header h2{font-size:1.12rem;font-weight:700;line-height:1.35}
        .dialog-body{padding:16px 22px 4px}
        .dialog-body > label{display:block;font-size:.86rem;font-weight:600;color:var(--ink-700);margin-bottom:6px}
        .autocomplete-wrap{position:relative}
        #inpLocation{width:100%;height:46px;padding:0 14px;border:1px solid var(--line);border-radius:var(--r-field);background:var(--page-bg);font-size:1.02rem;font-weight:600;outline:0;transition:.15s}
        #inpLocation:focus{background:#fff;border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light)}
        .suggest-panel{display:none;position:absolute;left:0;right:0;top:calc(100% + 6px);background:#fff;border:1px solid var(--border);border-radius:12px;box-shadow:0 12px 32px rgba(15,23,42,.16);max-height:min(280px,40vh);overflow-y:auto;z-index:50;padding:6px}
        .suggest-panel.open{display:block}
        .suggest-item{padding:8px 10px;font-size:.95rem;font-weight:600;cursor:pointer;border-radius:8px}
        .suggest-item:hover,.suggest-item.hl{background:var(--primary-light);color:var(--primary)}
        .suggest-empty{padding:12px 14px;font-size:.88rem;color:var(--muted);text-align:center}
        .hint{margin-top:10px;font-size:.88rem;color:var(--muted);background:var(--page-bg);border-left:3px solid var(--border);border-radius:0 8px 8px 0;padding:8px 12px}
        .chips-section{margin-top:14px}
        .chips-label{font-size:.8rem;font-weight:600;color:var(--faint);margin-bottom:6px}
        .chips{display:flex;flex-wrap:wrap;gap:6px}
        .chip{display:inline-flex;align-items:center;height:32px;padding:0 13px;border:1px solid var(--line);border-radius:999px;background:#fff;font-weight:700;font-size:.88rem;color:var(--ink-700);cursor:pointer;transition:.15s}
        .chip:hover{border-color:var(--primary);color:var(--primary);background:var(--primary-light)}
        .dialog-actions{display:flex;gap:10px;justify-content:flex-end;padding:18px 22px 20px}
        .btn-ghost{height:42px;padding:0 18px;border:1px solid var(--line);border-radius:var(--r-field);background:#fff;color:var(--ink-700);font-weight:600}
        .btn-ghost:hover{background:var(--soft)}
        .btn-primary{height:42px;padding:0 20px;border:0;border-radius:var(--r-field);background:var(--primary);color:#fff;font-weight:700}
        .btn-primary:hover{background:var(--primary-dark)}
        .btn-primary:disabled{opacity:.7;cursor:progress}

        /* ป๊อปอัพรายการสินค้า */
        #itemsModal{width:min(94vw,620px);overflow:hidden;display:none;flex-direction:column}
        #itemsModal[open]{display:flex}
        #itemsModal .dialog-body{overflow-y:auto;overflow-x:hidden;padding-bottom:0}
        .so-group{margin-bottom:14px}
        .so-tag{display:inline-block;background:var(--primary-light);color:var(--primary);font-weight:700;font-size:.84rem;padding:2px 12px;border-radius:999px;margin-bottom:6px}
        .items-tbl{width:100%;min-width:0;table-layout:auto;border:1px solid var(--line);border-radius:10px;border-collapse:separate;border-spacing:0;overflow:hidden}
        .items-tbl th{background:var(--soft);color:var(--ink-700);font-size:.8rem;padding:8px 14px;border-right:0}
        .items-tbl td{padding:9px 14px;border-right:0;font-size:.9rem}
        .items-tbl tr:last-child td{border-bottom:0}
        .items-tbl .nm{text-align:left;font-weight:500}
        .items-tbl .col-fit{width:1%;white-space:nowrap;text-align:right;font-variant-numeric:tabular-nums;font-weight:700}
        .items-modal-empty,.items-modal-loading{padding:34px 10px;text-align:center;color:var(--muted)}
        .items-modal-loading .spin{width:30px;height:30px;margin:0 auto 10px;border-radius:50%;border:3px solid var(--primary-light);border-top-color:var(--primary);animation:spin .8s linear infinite}
        @keyframes spin{to{transform:rotate(360deg)}}

        /* ===== กล่องยืนยัน / แจ้งเตือน (แทน confirm / alert) ===== */
        dialog.ui-dlg{width:min(92vw,420px);padding:26px 24px 20px;text-align:center}
        .ui-dlg-icon{width:56px;height:56px;border-radius:50%;margin:0 auto 14px;display:flex;align-items:center;justify-content:center;background:var(--primary);color:#fff;box-shadow:0 0 0 6px var(--primary-light)}
        .ui-dlg-icon svg{width:26px;height:26px;stroke-width:2.4}
        .ui-dlg.t-success .ui-dlg-icon{background:var(--success);box-shadow:0 0 0 6px var(--success-light)}
        .ui-dlg-title{font-size:1.2rem;font-weight:700}
        .ui-dlg-msg{margin-top:6px;color:var(--muted);font-size:.95rem}
        .ui-dlg-msg:empty{display:none}
        .ui-dlg-detail{margin-top:14px;background:var(--page-bg);border:1px solid var(--line);border-radius:12px;padding:10px 14px;text-align:left;display:grid;grid-template-columns:auto 1fr;gap:6px 16px}
        .ui-dlg-detail:empty{display:none}
        .ui-dlg-detail dt{color:var(--faint);font-size:.82rem;align-self:center}
        .ui-dlg-detail dd{margin:0;font-weight:700}
        .ui-dlg-actions{display:flex;gap:10px;margin-top:20px}
        .ui-dlg-actions button{flex:1}
        .ui-dlg.t-success #uiDlgOk{background:var(--success)}
        .ui-toast{position:fixed;inset:auto auto 24px 50%;margin:0;border:0;transform:translate(-50%,20px);opacity:0;pointer-events:none;transition:opacity .2s,transform .2s;width:max-content;max-width:92vw;z-index:450;
                  background:var(--ink);color:#fff;padding:12px 18px;border-radius:12px;font-size:.92rem;font-weight:500;box-shadow:0 10px 30px rgba(0,0,0,.25)}
        .ui-toast.show{opacity:1;transform:translate(-50%,0)}
        .ui-toast.t-error{background:var(--danger)}
        .ui-toast.t-success{background:var(--success)}


        /* ===== สีตามสถานะ ===== */
        .s-ready{--sc:#1a4fd6;--sb:#eaf0fd}.s-doing{--sc:#b45309;--sb:#fef3c7}.s-wait{--sc:#475569;--sb:#f1f5f9}.s-done{--sc:#166534;--sb:#dcfce7}
        .spill{display:inline-flex;align-items:center;gap:6px;padding:3px 11px;border-radius:999px;font-size:.8rem;font-weight:700;white-space:nowrap;color:var(--sc);background:var(--sb)}
        .spill::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
        tr[data-stage] td.c-chk{box-shadow:inset 4px 0 0 var(--sc)}
        .pso{display:flex;flex-direction:column;align-items:center;gap:3px}
        .pso .po-line{display:flex;align-items:center;gap:6px}
        .act-row{display:flex;gap:6px;justify-content:center;align-items:center;flex-wrap:wrap}

        /* ===== เลือกชั้นวางแบบกดปุ่ม ===== */
        .loc-search{position:relative}
        .loc-search svg{position:absolute;left:13px;top:50%;transform:translateY(-50%);width:17px;height:17px;color:var(--faint)}
        #inpLocation{padding-left:40px}
        .zones{display:flex;gap:5px;flex-wrap:wrap;margin:12px 0 10px}
        .zone{height:32px;padding:0 13px;border:1px solid var(--line);border-radius:999px;background:#fff;font-size:.86rem;font-weight:700;color:var(--ink-700);transition:.15s}
        .zone:hover{border-color:var(--border);background:var(--soft)}
        .zone.on{background:var(--ink);border-color:var(--ink);color:#fff}
        .shelf-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(92px,1fr));gap:6px;max-height:min(300px,38vh);overflow-y:auto;padding:2px}
        .shelf{height:46px;border:1px solid var(--line);border-radius:9px;background:#fff;font-weight:800;font-size:.95rem;color:var(--ink);padding:0 6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;transition:.12s}
        .shelf:hover{border-color:var(--primary);color:var(--primary);background:#f5f8ff}
        .shelf.recent{border-color:#c7d6fb;background:#f5f8ff}
        .shelf.on{background:var(--primary);border-color:var(--primary);color:#fff;box-shadow:0 4px 12px rgba(26,79,214,.3)}
        .shelf-empty{grid-column:1/-1;padding:22px;text-align:center;color:var(--muted);font-size:.9rem}
        @media (max-width:560px){ .shelf-grid{grid-template-columns:repeat(3,1fr)} }


        /* ===== แบบ D: ตารางซ้าย + แผงขึ้นชั้นขวา ===== */
        .split{display:grid;grid-template-columns:minmax(0,1fr) 400px;gap:14px;align-items:start}
        .split-main{min-width:0}
        .loc-panel{position:sticky;top:var(--stick-top,162px);background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:var(--shadow);overflow:hidden;transition:box-shadow .2s}
        .loc-panel.flash{box-shadow:0 0 0 4px #c7d6fb,var(--shadow)}
        .lp-head{display:flex;align-items:center;gap:10px;background:var(--primary);color:#fff;padding:14px 16px;font-weight:800;font-size:1.08rem}
        .lp-head svg{width:20px;height:20px}
        .lp-head .lp-cnt{margin-left:auto;background:#fff;color:var(--primary);border-radius:999px;padding:1px 12px;font-size:.88rem;font-variant-numeric:tabular-nums}
        .lp-close{display:none;width:34px;height:34px;border:0;border-radius:8px;background:rgba(255,255,255,.18);color:#fff;align-items:center;justify-content:center}
        .lp-body{padding:14px 16px 16px}
        .lp-label{font-size:.8rem;font-weight:700;color:var(--muted);margin:2px 0 6px}
        .sel-chips{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:12px;max-height:96px;overflow-y:auto}
        .sel-chip{display:inline-flex;align-items:center;gap:6px;height:28px;padding:0 4px 0 11px;border-radius:999px;background:var(--primary-light);color:var(--primary);font-weight:700;font-size:.82rem;white-space:nowrap}
        .sel-chip button{width:20px;height:20px;border:0;border-radius:50%;background:#fff;color:var(--primary);display:inline-flex;align-items:center;justify-content:center;font-size:.9rem;line-height:1}
        .sel-chip button:hover{background:var(--danger);color:#fff}
        .sel-empty{font-size:.86rem;color:var(--faint);background:var(--page-bg);border:1px dashed var(--border);border-radius:10px;padding:10px 12px;margin-bottom:12px}
        .loc-panel .hint{margin:0 0 12px}
        .loc-panel .hint:empty{display:none}
        .loc-panel #locSuggest,#recentChips{display:none !important}
        .loc-search{position:relative}
        .loc-search svg{position:absolute;left:13px;top:50%;transform:translateY(-50%);width:17px;height:17px;color:var(--faint)}
        #inpLocation{width:100%;height:44px;padding:0 14px 0 40px;border:1px solid var(--line);border-radius:var(--r-field);background:var(--page-bg);font-size:1rem;font-weight:600;outline:0;transition:.15s}
        #inpLocation:focus{background:#fff;border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light)}
        .zones{display:flex;gap:5px;flex-wrap:wrap;margin:10px 0}
        .zone{height:32px;padding:0 12px;border:1px solid var(--line);border-radius:999px;background:#fff;font-size:.85rem;font-weight:700;color:var(--ink-700);transition:.15s}
        .zone:hover{background:var(--soft)}
        .zone.on{background:var(--ink);border-color:var(--ink);color:#fff}
        .shelf-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;align-content:start;max-height:calc(100vh - 520px);min-height:150px;overflow-y:auto;padding:2px}
        .shelf{height:50px;border:1px solid var(--line);border-radius:9px;background:#fff;font-weight:800;font-size:1rem;color:var(--ink);padding:0 4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;transition:.12s}
        .shelf:hover{border-color:var(--primary);color:var(--primary);background:#f5f8ff}
        .shelf.recent{border-color:#c7d6fb;background:#f5f8ff}
        .shelf.on{background:var(--primary);border-color:var(--primary);color:#fff;box-shadow:0 4px 12px rgba(26,79,214,.3)}
        .shelf-empty{grid-column:1/-1;padding:22px;text-align:center;color:var(--muted);font-size:.9rem}
        .lp-save{margin-top:14px;width:100%;height:50px;border:0;border-radius:10px;background:var(--success);color:#fff;font-weight:800;font-size:1.04rem;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 4px 14px rgba(22,163,74,.25);transition:.15s}
        .lp-save svg{width:19px;height:19px;stroke-width:2.6}
        .lp-save:hover{background:var(--success-dark)}
        .lp-save:disabled{background:#cbd5e1;box-shadow:none;cursor:not-allowed}
        .lp-backdrop{display:none}
        tbody tr[data-stage]:has(.chkLine){cursor:pointer}
        tbody tr:has(.chkLine:checked) td{background:#e3ebfd}
        .po-line{display:flex;align-items:center;gap:6px;justify-content:center}
        .pso{display:flex;flex-direction:column;align-items:center;gap:2px}
        .btn-view-items.ic{width:28px;padding:0;justify-content:center}
        .meta-line{font-size:.8rem;color:var(--faint);margin-top:3px;display:flex;flex-wrap:wrap;gap:4px 6px;align-items:center}
        .meta-line .sep{color:var(--border)}
        /* จอไม่กว้าง: แผงกลายเป็นแผ่นเลื่อนขึ้นจากด้านล่าง (กดปุ่มลอย "ระบุตำแหน่ง") */
        @media (min-width:1101px){ #btnMain{display:none !important} }
        @media (max-width:1100px){
            .split{grid-template-columns:1fr}
            .loc-panel{position:fixed;left:0;right:0;bottom:0;top:auto;z-index:300;border-radius:18px 18px 0 0;max-height:92vh;overflow-y:auto;transform:translateY(105%);transition:transform .22s ease-out;box-shadow:0 -10px 40px rgba(15,23,42,.25)}
            .loc-panel.open{transform:none}
            .lp-close{display:inline-flex}
            .lp-backdrop{display:block;position:fixed;inset:0;z-index:290;background:rgba(15,23,42,.5);opacity:0;visibility:hidden;transition:.2s}
            .lp-backdrop.open{opacity:1;visibility:visible}
            .shelf-grid{max-height:40vh}
        }
        @media (max-width:560px){ .shelf-grid{grid-template-columns:repeat(3,1fr)} }

        /* ช่องค้นหาในแถวบน: ตัวอักษรเล็กลงนิด ไม่ให้ข้อความขาด */
        .tools .fld:not(.dd){padding:0 12px;gap:8px;min-width:240px;flex:1 1 300px}
        .tools .fld:not(.dd) input{font-size:.92rem}
        .tools .fld:not(.dd) > svg{width:15px;height:15px}
        .tools{flex-wrap:wrap}
        .tools .dd{flex:0 0 175px}
        @media (max-width:1850px){ .tools{order:3;flex-basis:100%} .vsep{display:none} .tools .dd{flex-basis:190px} }
        @media (max-width:1000px){
            .top-banner{position:static}
            .tools{flex-wrap:wrap}
            .tools .fld{flex:1 1 calc(33.33% - 6px)}
            .tools .dd{flex:1 1 calc(50% - 28px)}
            .tools .search-btn{flex:1 1 auto;justify-content:center}
        }
        @media (max-width:560px){
            .user-badge .name{display:none}
            .banner-right{width:100%}
            .todo-alert{flex:1;justify-content:center}
            .tools .fld,.tools .fld:not(.dd){flex-basis:100%}
            .tools .dd{flex:1 1 calc(50% - 28px)}
            .table-topbar .tip{display:none}
            .search-btn .sb-kbd{display:none}
            .float-action{width:calc(100% - 32px);justify-content:space-between}
        }
        /* จอเล็ก: ตาราง -> การ์ดทีละใบ */
        @media (max-width:760px){
            table{min-width:0}
            thead{display:none}
            table,tbody{display:block}
            tbody tr{position:relative;display:grid;grid-template-columns:1fr 1fr;gap:10px 14px;padding:14px 16px;border-bottom:1px solid var(--line)}
            tbody tr:has(td.empty){display:block;padding:0}
            td{display:block;border:0 !important;padding:0;text-align:left}
            td[data-label]::before{content:attr(data-label);display:block;font-size:.72rem;font-weight:600;color:var(--faint);margin-bottom:2px}
            td.c-chk{position:absolute;top:12px;right:14px;width:auto}
            td.c-chk input{width:22px;height:22px}
            td.c-po{grid-column:1/-1;padding-right:36px}
            .pso{align-items:flex-start}
            td.c-stage{grid-column:1/-1}
            .act-row{justify-content:flex-start}
            tr[data-stage]{box-shadow:inset 4px 0 0 var(--sc)}
            tr[data-stage] td.c-chk{box-shadow:none}
            .po-cell{flex-direction:row;flex-wrap:wrap;align-items:center;gap:8px}
            td.c-manage{grid-column:1/-1}
            .manage{align-items:flex-start}
            .cust-cell .nm{max-width:none}
        }
        @media (prefers-reduced-motion:reduce){ .todo-alert,.float-action{animation:none} }
    </style>
</head>
<body>
<div class="page-frame">
    <div class="top-banner">
        <a class="h1" href="" title="รีเฟรชหน้า"><span class="logo"><svg class="i" viewBox="0 0 24 24"><path d="M3 21V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v16"/><path d="M3 11h18M3 17h18M8 7h3M8 14h3"/></svg></span>จัดบิลขึ้นชั้น</a>
        <span class="vsep"></span>
        <div class="tools">
            {{-- ช่องค้นหาเดียว: SO / PO / ลูกค้า (ช่องเดิม 3 ช่องยังอยู่แบบซ่อน — ระบบแยกให้เองว่าพิมพ์อะไร) --}}
            <label class="fld f-all" for="searchAll" title="ค้นหาเลข SO / เลข PO / ชื่อลูกค้า">
                <svg class="i" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                <input type="search" id="searchAll" value="{{ request('SONum') ?: (request('PONum') ?: request('customer')) }}" placeholder="ค้นหา เลข SO / เลข PO / ชื่อลูกค้า" autocomplete="off">
            </label>
            <input type="hidden" id="searchSO" value="{{ request('SONum') }}">
            <input type="hidden" id="searchPO" value="{{ request('PONum') }}">
            <input type="hidden" id="searchCustomer" value="{{ request('customer') }}">
            <button type="button" class="search-btn" id="btnSearch" title="ค้นหาทุกหน้า (หรือกด Enter ในช่องค้นหา)">
                <span class="sb-ic"><svg class="i" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg></span>
                <span class="sb-spin" aria-hidden="true"></span>
                <span class="sb-txt">ค้นหา</span>
                <kbd class="sb-kbd">Enter</kbd>
            </button>
            {{-- ดรอปดาวน์ (select เดิมซ่อนอยู่ข้างใน ยังเป็นตัวเก็บค่า / บอทตั้งค่า .value ได้เหมือนเดิม) --}}
            <div class="fld dd" id="ddPoType" title="ประเภท PO">
                <span class="st-dot"></span>
                <select id="filterPoType" class="dd-native" tabindex="-1" aria-hidden="true">
                    <option value="">PO ทั้งหมด</option>
                    <option value="internal" {{ request('po_type') === 'internal' ? 'selected' : '' }}>ภายใน</option>
                    <option value="external" {{ request('po_type') === 'external' ? 'selected' : '' }}>ภายนอก</option>
                </select>
                <button type="button" class="dd-btn" aria-haspopup="listbox"><span class="dd-text">PO ทั้งหมด</span></button>
                <span class="caret"><svg class="i" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg></span>
                <div class="dd-panel" role="listbox"></div>
            </div>
            <div class="fld dd" id="ddHandler" title="ผู้จัดการ">
                <span class="st-dot"></span>
                <select id="filterHandler" class="dd-native" tabindex="-1" aria-hidden="true">
                    <option value="">ผู้จัดการทั้งหมด</option>
                    <option value="โอ">โอ</option>
                    <option value="ฟิว">ฟิว</option>
                </select>
                <button type="button" class="dd-btn" aria-haspopup="listbox"><span class="dd-text">ผู้จัดการทั้งหมด</span></button>
                <span class="caret"><svg class="i" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg></span>
                <div class="dd-panel" role="listbox"></div>
            </div>
            <div class="fld dd" id="ddStage" title="สถานะ">
                <span class="st-dot"></span>
                <select id="filterStage" class="dd-native" tabindex="-1" aria-hidden="true">
                    <option value="">สถานะทั้งหมด</option>
                    <option value="ready">พร้อมระบุตำแหน่ง</option>
                    <option value="doing">กำลังจัดการ</option>
                    <option value="wait">รอดำเนินการ</option>
                    <option value="done">ขึ้นชั้นแล้ว</option>
                </select>
                <button type="button" class="dd-btn" aria-haspopup="listbox"><span class="dd-text">สถานะทั้งหมด</span></button>
                <span class="caret"><svg class="i" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg></span>
                <div class="dd-panel" role="listbox"></div>
            </div>
            <button type="button" class="btn-icon reset" id="btnClear" title="ล้างตัวกรอง"><svg class="i" viewBox="0 0 24 24"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg></button>
        </div>
        <div class="banner-right">
            <div class="todo-alert{{ $totalTodo ? '' : ' zero' }}" id="todoBlock" title="จำนวนใบที่รอระบุตำแหน่ง">
                <span class="oa-icon"><svg class="i" viewBox="0 0 24 24"><path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg></span>รอระบุตำแหน่ง <b id="todoCount">{{ $totalTodo }}</b>
            </div>
            <span class="user-badge" title="ผู้ใช้งาน"><span class="av"><svg class="i" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span><span class="name">{{ $creator }}</span></span>
            <a href="http://server_update:8000/solist" class="btn-icon" title="หน้าหลัก"><svg class="i" viewBox="0 0 24 24"><path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/></svg></a>
        </div>
    </div>
    <input type="hidden" id="inpUser" value="{{ $creator }}">
    @php $nav = $creator ? ['create_by' => $creator] : []; @endphp
    <main>
        {{-- ตัวนับจำนวนที่แสดง (ซ่อนไว้ ยังใช้กับ liveFilter) --}}
        <span id="showCount" hidden>{{ $heads->total() }}</span>

        <div class="split">
        <div class="split-main">
        <div class="table-scroll">
            <div class="table-inner">
                <table>
                    <thead>
                        <tr>
                            <th style="width:52px;"><input type="checkbox" id="chkAll" title="เลือกทั้งหมด"></th>
                            <th>PO / SO</th>
                            <th>ลูกค้า / ร้านค้า</th>
                            <th>สถานะ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse ($heads as $h)
                            @php
                                $todo        = $h->todo;
                                $cls         = $todo ? '' : 'done';
                                $items       = $h->items;
                                $totalQty    = $h->total_qty;
                                $location    = $h->location;
                                $checkboxVal = $h->type . ':' . $h->id;
                                $isClaimed   = $h->type === 'external' && ($h->claimed ?? false);
                                $isFinished  = $h->type === 'external' && ($h->finished ?? false);
                                $poType      = str_contains((string) $h->po_display, 'A') ? 'internal' : 'external';
                                // internal_po: PENDING+claim แล้ว = กำลังจัดการ (ต้องกด "จัดการเสร็จสิ้น") / FINISH = พร้อมระบุตำแหน่ง
                                $rowStatus       = $h->status ?? null;
                                $internalPending = $h->type === 'internal' && $rowStatus === \App\Models\internal_po::ST_PENDING;
                                $internalReady   = $h->type === 'internal' && $rowStatus === \App\Models\internal_po::ST_FINISH;
                                // เลือก checkbox เพื่อระบุตำแหน่งได้เฉพาะ: internal ที่พร้อม, หรือ external/legacy ที่ยังไม่ถูก claim
                                $canSelect       = $h->type === 'internal' ? $internalReady : ($todo && !$isClaimed);
                                // ขั้นของใบ (ใช้แสดงสถานะ/แท็บ): ready = พร้อมระบุตำแหน่ง, doing = กำลังจัดการ, wait = รอดำเนินการ, done = ขึ้นชั้นแล้ว
                                if (!$todo)                                  $stage = 'done';
                                elseif ($isClaimed || $internalPending)     $stage = 'doing';
                                elseif ($isFinished || $internalReady)      $stage = 'ready';
                                else                                         $stage = 'wait';
                                $stageLabel = ['ready' => 'พร้อมระบุตำแหน่ง', 'doing' => 'กำลังจัดการ', 'wait' => 'รอดำเนินการ', 'done' => 'ขึ้นชั้นแล้ว'][$stage];
                                $itemsJson = $items
                                    ? $items->map(fn ($it) => [
                                        'name' => $it->item_name,
                                        'qty'  => (float) $it->item_quantity,
                                        'so'   => $it->so ?? $it->so_id ?? null,
                                    ])->values()
                                    : null;
                            @endphp
                            <tr class="{{ $cls }} s-{{ $stage }}" data-done="{{ $todo ? 0 : 1 }}"
                                data-stage="{{ $stage }}"
                                data-so="{{ $h->so_id }}"
                                data-po="{{ $h->po_display }}"
                                data-customer="{{ $h->customer_name }}"
                                data-po-type="{{ $poType }}"
                                data-handler="{{ $h->claimed_by ?? '' }}">
                                <td class="c-chk">
                                    @if ($canSelect)<input type="checkbox" class="chkLine" value="{{ $checkboxVal }}">@endif
                                </td>
                                <td class="c-po" data-label="PO / SO">
                                    <div class="pso">
                                        <div class="po-line">
                                            <span class="ref-link">{{ $h->po_display }}</span>
                                            {{-- ปุ่มดูสินค้า: มี items แล้วฝังไว้ (ไม่ต้อง fetch) / ไม่มี = โหลดตอนกด
                                                 ส่ง data-so ไปด้วย เพราะ PO เดียวกันมีได้หลาย SO → ต้องกรองให้เห็นเฉพาะ SO ของแถวนี้ --}}
                                            <button type="button" class="btn-view-items ic" title="ดูสินค้า @if ($items) ({{ $items->count() }} รายการ) @endif"
                                                data-po="{{ $h->po_display }}" data-so="{{ $h->so_id }}"
                                                @if ($itemsJson !== null) data-items='@json($itemsJson)' @endif><svg class="i" viewBox="0 0 24 24"><path d="M21 8 12 3 3 8v8l9 5 9-5z"/><path d="m3 8 9 5 9-5M12 13v8"/></svg></button>
                                        </div>
                                        @if (!empty($h->so_id))
                                            <a class="so-link" href="http://server_update:8000/sodetail?SONum={{ urlencode($h->so_id) }}" target="_blank" title="เปิดรายละเอียด SO">{{ $h->so_id }}</a>
                                        @else
                                            <span class="dash">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="cust-cell" data-label="ลูกค้า / ร้านค้า">
                                    <div class="nm">{{ $h->customer_name ?: '-' }}</div>
                                    <div class="meta-line">
                                        @if ($poType === 'internal')
                                            <span class="po-type-badge po-internal">ภายใน</span>
                                        @else
                                            <span class="po-type-badge po-external">ภายนอก</span>
                                            <span>{{ $h->vendor_name ?: '-' }}@if(!empty($h->vendor_code)) ({{ $h->vendor_code }})@endif</span>
                                        @endif
                                        <span class="sep">·</span><span>Sale {{ $h->sale ?: '—' }}</span>
                                        <span class="sep">·</span><span>รับโดย {{ $h->packed_by ?: '—' }}{{ $h->packed_at ? ' ' . \Carbon\Carbon::parse($h->packed_at)->format('d/m H:i') : '' }}</span>
                                    </div>
                                </td>
                                <td class="c-stage" data-label="สถานะ">
                                    <span class="spill">{{ $stageLabel }}</span>
                                </td>
                                <td class="c-manage" data-label="ดำเนินการ">
                                    <div class="manage">
                                    @if ($h->type === 'external' || $h->type === 'legacy')
                                        @if ($isClaimed)
                                            {{-- do_it: มีคนเอาของออกไปทำ (ยังไม่กดรับคืน) --}}
                                            <button type="button" class="btn-finish-claim" data-po="{{ $h->id }}" data-checkbox="{{ $h->type }}:{{ $h->id }}"><svg class="i" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>จัดการเสร็จสิ้น</button>
                                            <div class="muted">เอาไปทำโดย {{ $h->claimed_by ?: '—' }}{{ $h->claimed_at ? ' · ' . \Carbon\Carbon::parse($h->claimed_at)->format('d/m/Y H:i') : '' }}</div>
                                        @elseif ($isFinished)
                                            {{-- ผู้ดูแลกดรับของกลับแล้ว (sus) → พร้อมระบุตำแหน่ง --}}
                                            <span class="finished-tag">พร้อม</span>
                                            <div class="muted">เอาไปทำโดย {{ $h->claimed_by ?: '—' }}{{ $h->claimed_at ? ' · ' . \Carbon\Carbon::parse($h->claimed_at)->format('d/m/Y H:i') : '' }}</div>
                                            <div class="muted">รับคืนโดย {{ $h->finished_by ?: '—' }}{{ $h->finished_at ? ' · ' . \Carbon\Carbon::parse($h->finished_at)->format('d/m/Y H:i') : '' }}</div>
                                        @else
                                            <div class="act-row">
                                                {{-- เลือกชื่อผู้จัดการ (โอ/ฟิว) -> บันทึกเข้า do_it --}}
                                                <select class="claim-select" data-po="{{ $h->id }}" data-type="{{ $h->type }}">
                                                    <option value="">กำลังจัดการ...</option>
                                                    <option value="โอ">โอ</option>
                                                    <option value="ฟิว">ฟิว</option>
                                                </select>
                                            </div>
                                        @endif
                                    @else
                                        {{-- internal_po --}}
                                        @if ($internalPending)
                                            <button type="button" class="btn-finish-claim" data-po="{{ $h->id }}" data-internal="1" data-checkbox="internal:{{ $h->id }}"><svg class="i" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>จัดการเสร็จสิ้น</button>
                                            <div class="muted">โดย {{ $h->claimed_by ?: '—' }}{{ $h->claimed_at ? ' · ' . \Carbon\Carbon::parse($h->claimed_at)->format('d/m/Y H:i') : '' }}</div>
                                        @elseif ($internalReady)
                                            <span class="finished-tag">พร้อม</span>
                                        @else
                                            @if ($location)
                                                <span class="loc-tag"><svg class="i" viewBox="0 0 24 24"><path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg>{{ $location }}</span>
                                            @else
                                                <span class="dash">—</span>
                                            @endif
                                        @endif
                                    @endif
                                    @if (!$todo && $location && $h->type !== 'internal')
                                        <span class="loc-tag"><svg class="i" viewBox="0 0 24 24"><path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg>{{ $location }}</span>
                                    @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="empty"><svg class="i" viewBox="0 0 24 24"><path d="M3 21V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v16"/><path d="M3 11h18M3 17h18"/></svg>ไม่มีรายการที่รอระบุตำแหน่ง</td></tr>
                        @endforelse
                        <tr class="no-match" hidden><td colspan="5" class="empty">ไม่มีใบในสถานะนี้</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        @if ($heads->hasPages())
            <div class="pagination">
                @if ($heads->onFirstPage())
                    <span class="page-btn disabled">‹ ก่อนหน้า</span>
                @else
                    <a class="page-btn" href="{{ $heads->previousPageUrl() }}">‹ ก่อนหน้า</a>
                @endif

                <span class="page-info">หน้า {{ $heads->currentPage() }} / {{ $heads->lastPage() }} (ทั้งหมด {{ $heads->total() }} ใบ)</span>

                @if ($heads->hasMorePages())
                    <a class="page-btn" href="{{ $heads->nextPageUrl() }}">ถัดไป ›</a>
                @else
                    <span class="page-btn disabled">ถัดไป ›</span>
                @endif
            </div>
        @endif
        </div>

        {{-- แผงขึ้นชั้น (id="locModal" เดิม — openModal() / confirmLoc() ใช้ได้เหมือนเดิม) --}}
        <div class="lp-backdrop" id="lpBackdrop" onclick="closePanel()"></div>
        <aside class="loc-panel" id="locModal" aria-label="ระบุตำแหน่งจัดเก็บ">
            <div class="lp-head"><svg class="i" viewBox="0 0 24 24"><path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg>ขึ้นชั้น<span class="lp-cnt" id="lpCount">0 ใบ</span>
                <button type="button" class="lp-close" onclick="closePanel()" title="ปิด"><svg class="i" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
            </div>
            <div class="lp-body">
                <div class="lp-label">ใบที่เลือก</div>
                <div class="sel-chips" id="selChips"></div>
                <p class="hint" id="dlgHint"></p>
                <div class="lp-label">เลือกชั้นวาง</div>
                <div class="autocomplete-wrap loc-search">
                    <svg class="i" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                    <input type="text" id="inpLocation" placeholder="พิมพ์ค้นหา เช่น A11" autocomplete="off" maxlength="100">
                    <div id="locSuggest" class="suggest-panel"></div>
                </div>
                <div class="zones" id="zoneBar"></div>
                <div class="shelf-grid" id="shelfGrid"></div>
                {{-- ชั้นที่ใช้ล่าสุด (ใช้ทำโซน "ใช้ล่าสุด") --}}
                <div class="chips" id="recentChips" hidden>
                    @foreach (($locations ?? collect())->take(6) as $loc)
                        <span class="chip" onclick="pickLoc(this)">{{ $loc }}</span>
                    @endforeach
                </div>
                <button type="button" class="btn-primary lp-save" onclick="confirmLoc()">บันทึกตำแหน่ง</button>
            </div>
        </aside>
        </div>
    </main>
</div>

{{-- ปุ่มลอย: โผล่เมื่อติ๊กเลือกรายการ (id / onclick / #selCount เหมือนเดิม) --}}
<button type="button" class="btn-success float-action" id="btnMain" hidden onclick="openModal()">
    <svg class="i" viewBox="0 0 24 24"><path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg>ระบุตำแหน่ง <span class="fa-cnt"><span id="selCount">0</span> ใบ</span>
</button>


<dialog id="itemsModal">
    <div class="dialog-header">
        <span class="d-icon"><svg class="i" viewBox="0 0 24 24"><path d="M21 8 12 3 3 8v8l9 5 9-5z"/><path d="m3 8 9 5 9-5M12 13v8"/></svg></span>
        <h2 id="itemsModalTitle">รายการสินค้า</h2>
    </div>
    <div class="dialog-body">
        <div id="itemsModalBody"></div>
    </div>
    <div class="dialog-actions">
        <button type="button" class="btn-ghost" onclick="document.getElementById('itemsModal').close()">ปิด</button>
    </div>
</dialog>

<!-- กล่องยืนยัน (เป็น dialog เพื่อให้ซ้อนบนป๊อปอัพอื่นได้) -->
<dialog id="uiDialog" class="ui-dlg">
    <div class="ui-dlg-icon"></div>
    <div class="ui-dlg-title" id="uiDlgTitle"></div>
    <div class="ui-dlg-msg" id="uiDlgMsg"></div>
    <dl class="ui-dlg-detail" id="uiDlgDetail"></dl>
    <div class="ui-dlg-actions">
        <button type="button" class="btn-ghost" id="uiDlgCancel">ยกเลิก</button>
        <button type="button" class="btn-primary" id="uiDlgOk">ยืนยัน</button>
    </div>
</dialog>
<div id="uiToast" class="ui-toast" role="status" aria-live="polite" popover="manual"></div>

<script>
const SUBMIT_URL = "{{ route('store.location.submit') }}";
const CLAIM_URL  = "{{ route('store.location.claim') }}";
const FINISH_URL = "{{ route('store.location.finish') }}";
const FINISH_INTERNAL_URL = "{{ route('store.location.finishInternal') }}";
const LEGACY_ITEMS_URL = "{{ route('store.location.legacyItems') }}";
const LEGACY_CLAIM_URL = "{{ route('store.location.legacyClaim') }}";
const CSRF       = document.querySelector('meta[name="csrf-token"]').content;
const modal      = document.getElementById('locModal');   // แผงขึ้นชั้น
const SHELF_OPTIONS = [
  "1A01","1Kโบว์","1กวาง","1กี้","1ตี๋/พลอย","1ต่าย","1ท๊อป","1นภา",
  "1นุ/เต้น","1นุช","1นุ่น","1น้อย/มล","1น้ำ/กิ๊ฟ","1ฟอง","1ฟิล์ม",
  "1ภิรุณ","1มุก","1หนิง","1หมิง","1หมู/ต๋อง","1เจี๊ยบ","1เชร์",
  "1เนย","1เอก","1แยม","1แอม","1โจ",
  "A11","A12","A13","A14","A21","A22","A23","A24",
  "A31","A32","A33","A34","A41","A42","A43","A44",
  "aom stock","B1",
  "C1","C10","C11","C12","C13","C14","C15","C16",
  "C2","C3","C4","C5","C6","C7","C8","C9","Cท่อ",
  "LASADA",
  "Qกรมศุลเล็","Qจัดแล้วรอ","Qติดปัญหา","Qบิลสด","QรอครบSO","Qสหกรณ์","Qแก้ไข","Qแดง",
  "top stock","Z55",
  "ขมจ่ายแล้ว","ของเกิน","คืนstock","ช.เดช","ช.โอ","ชัย-เดช","ชัย1","ชัย2",
  "ด.1","ด.10","ด.11","ด.12","ด.2","ด.3","ด.4","ด.5","ด.6","ด.7","ด.8","ด.9",
  "ทำคืน","ท๊อปบน","ปอ-ฮิคาริ","ปิดรับบิล","ปี69","พู่",
  "ว๊าล","ว๊าลแก้ไข","หน้าออฟฟิศ","หยรอบิล","หยกรอเคลีย","หลังออฟฟิศ","พู่/เอ็ม","ว้าล/เอ็ม"
];

// ===== กล่องยืนยัน + แจ้งเตือน (แทน confirm / alert ของเบราว์เซอร์) =====
const IC_CHECK = '<svg class="i" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>';
const IC_PIN   = '<svg class="i" viewBox="0 0 24 24"><path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg>';
const IC_USER  = '<svg class="i" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
function esc(s){ return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }
function uiConfirm(o){
    return new Promise(resolve => {
        const dlg = document.getElementById('uiDialog');
        const ok = document.getElementById('uiDlgOk'), cancel = document.getElementById('uiDlgCancel');
        dlg.className = 'ui-dlg t-' + (o.tone || 'info');
        dlg.querySelector('.ui-dlg-icon').innerHTML = o.icon || IC_CHECK;
        document.getElementById('uiDlgTitle').textContent = o.title || '';
        document.getElementById('uiDlgMsg').textContent = o.message || '';
        document.getElementById('uiDlgDetail').innerHTML = (o.detail || []).map(d => '<dt>' + esc(d[0]) + '</dt><dd>' + esc(d[1]) + '</dd>').join('');
        ok.textContent = o.okText || 'ยืนยัน';
        let settled = false;
        const done = v => { if (settled) return; settled = true; ok.onclick = cancel.onclick = dlg.oncancel = dlg.onclick = null; if (dlg.open) dlg.close(); resolve(v); };
        ok.onclick = () => done(true);
        cancel.onclick = () => done(false);
        dlg.oncancel = e => { e.preventDefault(); done(false); };       // กด Esc
        dlg.onclick = e => { if (e.target === dlg) done(false); };       // กดพื้นหลัง
        dlg.showModal();
        setTimeout(() => ok.focus(), 30);                                // Enter = ยืนยัน
    });
}
let toastTimer = null;
function uiToast(text, tone){
    const t = document.getElementById('uiToast');
    t.className = 'ui-toast' + (tone ? ' t-' + tone : '');
    t.textContent = text;
    try { if (t.showPopover && !t.matches(':popover-open')) t.showPopover(); } catch (e) {}
    requestAnimationFrame(() => t.classList.add('show'));
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => { t.classList.remove('show'); setTimeout(() => { try { t.hidePopover && t.hidePopover(); } catch (e) {} }, 220); }, 3200);
}

const inpLocation = document.getElementById('inpLocation');
const suggestPanel = document.getElementById('locSuggest');
const shelfGrid = document.getElementById('shelfGrid');
const zoneBar = document.getElementById('zoneBar');
const saveBtn = document.querySelector('#locModal .lp-save');
const RECENT = Array.from(document.querySelectorAll('#recentChips .chip')).map(c => c.textContent.trim()).filter(v => SHELF_OPTIONS.includes(v));
// โซนชั้นวาง (กดเลือกโซน แล้วกดปุ่มชั้น)
const ZONES = [
    { key: 'recent', label: 'ใช้ล่าสุด', test: s => RECENT.includes(s) },
    { key: 'A',      label: 'A',         test: s => /^A\d/.test(s) },
    { key: 'C',      label: 'C',         test: s => /^C/.test(s) },
    { key: 'D',      label: 'ด.',        test: s => s.startsWith('ด.') },
    { key: 'Q',      label: 'Q',         test: s => s.startsWith('Q') },
    { key: 'P',      label: 'ชั้นคน',     test: s => /^1/.test(s) },
];
const BASE_ZONES = ZONES.filter(z => z.key !== 'recent');
ZONES.push({ key: 'other', label: 'อื่น ๆ', test: s => !BASE_ZONES.some(z => z.test(s)) });
if (!RECENT.length) ZONES.shift();
let curZone = ZONES[0].key;

function updateSaveBtn() {
    const v = inpLocation.value.trim(), n = selectedIds().length, ok = SHELF_OPTIONS.includes(v);
    saveBtn.disabled = !ok || !n;
    saveBtn.innerHTML = !n ? 'เลือกใบที่จะขึ้นชั้นก่อน' : ok ? '<svg class="i" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>บันทึก ' + esc(v) + ' ให้ ' + n + ' ใบ' : 'เลือกชั้นวางก่อน';
}
function renderZones() {
    zoneBar.innerHTML = ZONES.map(z => '<button type="button" class="zone' + (z.key === curZone ? ' on' : '') + '" data-z="' + z.key + '">' + z.label + '</button>').join('');
}
function renderShelves() {
    const q = inpLocation.value.trim().toLowerCase();
    const cur = inpLocation.value.trim();
    // พิมพ์ค้นหา = ค้นทุกโซน / ไม่พิมพ์ = แสดงตามโซนที่เลือก
    const exact = SHELF_OPTIONS.includes(cur);
    let list = (q && !exact) ? SHELF_OPTIONS.filter(s => s.toLowerCase().includes(q))
                             : SHELF_OPTIONS.filter(ZONES.find(z => z.key === curZone).test);
    if (curZone === 'recent' && !(q && !exact)) list = RECENT.slice();
    zoneBar.querySelectorAll('.zone').forEach(b => b.classList.toggle('on', !(q && !exact) && b.dataset.z === curZone));
    shelfGrid.innerHTML = list.length
        ? list.map(s => '<button type="button" class="shelf' + (s === cur ? ' on' : '') + (RECENT.includes(s) ? ' recent' : '') + '" data-val="' + esc(s) + '" title="' + esc(s) + '">' + esc(s) + '</button>').join('')
        : '<div class="shelf-empty">ไม่พบชั้นวางที่ตรงกับคำค้นหา</div>';
    updateSaveBtn();
}
zoneBar.addEventListener('click', e => {
    const b = e.target.closest('.zone'); if (!b) return;
    curZone = b.dataset.z;
    if (!SHELF_OPTIONS.includes(inpLocation.value.trim())) inpLocation.value = '';
    renderShelves();
});
shelfGrid.addEventListener('click', e => {
    const b = e.target.closest('.shelf'); if (!b) return;
    inpLocation.value = b.dataset.val;
    renderShelves();
    saveBtn.focus();
});
inpLocation.addEventListener('input', renderShelves);
inpLocation.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        // พิมพ์แล้วเหลือชั้นเดียว -> เลือกให้เลย
        const only = shelfGrid.querySelectorAll('.shelf');
        if (!SHELF_OPTIONS.includes(inpLocation.value.trim()) && only.length === 1) { inpLocation.value = only[0].dataset.val; renderShelves(); return; }
        confirmLoc();
    }
});

const selectedIds = () => Array.from(document.querySelectorAll('.chkLine:checked')).map(c => c.value);
const currentUser = () => document.getElementById('inpUser').value.trim();

function refreshBtn() {
    const n = selectedIds().length;
    document.getElementById('selCount').textContent = n;
    document.getElementById('btnMain').hidden = (n === 0);
    renderSelChips();
}
// ใบที่เลือก -> โชว์ในแผงขึ้นชั้น (กด × เพื่อเอาออก)
function renderSelChips() {
    const ids = selectedIds();
    document.getElementById('lpCount').textContent = ids.length + ' ใบ';
    document.getElementById('selChips').innerHTML = ids.length
        ? ids.map(v => {
            const tr = document.querySelector('.chkLine[value="' + v.replace(/"/g, '\\"') + '"]')?.closest('tr');
            return '<span class="sel-chip">' + esc(tr?.dataset.po || v) + '<button type="button" data-v="' + esc(v) + '" title="เอาออก">×</button></span>';
          }).join('')
        : '';
    document.getElementById('selChips').hidden = !ids.length;
    if (!ids.length) setHint('ติ๊กเลือกใบทางซ้าย (หรือกดที่แถว) แล้วเลือกชั้นวาง', false);
    else if (!document.getElementById('dlgHint').dataset.err) setHint('', false);
    updateSaveBtn();
}
document.getElementById('selChips').addEventListener('click', e => {
    const b = e.target.closest('button[data-v]'); if (!b) return;
    const c = document.querySelector('.chkLine[value="' + b.dataset.v.replace(/"/g, '\\"') + '"]');
    if (c) { c.checked = false; refreshBtn(); }
});
// กดที่แถว = ติ๊ก/เอาติ๊กออก (ยกเว้นกดปุ่ม/ลิงก์/ดรอปดาวในแถว)
document.getElementById('tableBody').addEventListener('click', e => {
    if (e.target.closest('a,button,select,input,label')) return;
    const c = e.target.closest('tr')?.querySelector('.chkLine');
    if (!c || c.disabled) return;
    c.checked = !c.checked;
    refreshBtn();
});
document.getElementById('chkAll').addEventListener('change', function () {
    document.querySelectorAll('.chkLine:not([disabled])').forEach(c => c.checked = this.checked);
    refreshBtn();
});
document.querySelectorAll('.chkLine').forEach(c => c.addEventListener('change', refreshBtn));

function pickLoc(el) { const i = document.getElementById('inpLocation'); i.value = el.textContent.trim(); renderShelves(); i.focus(); }

// confirmOpts = ข้อความ (string) หรือ { title, message, detail, ... } ของกล่องยืนยัน
async function postClaimAction(url, poId, btn, confirmOpts, fieldName = 'po_id', extra = {}, autoLoc = null) {
    const opts = typeof confirmOpts === 'string' ? { title: confirmOpts } : confirmOpts;
    if (!(await uiConfirm(opts))) return false;
    btn.disabled = true;
    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type':'application/json','Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF },
            body: JSON.stringify({ [fieldName]: poId, ...extra })
        });
        const data = await res.json();
        if (res.ok && data.ok) {
            // จำใบที่เพิ่ง "จัดการเสร็จสิ้น" ไว้ -> หลังรีโหลดจะเปิดเลือกชั้นวางให้ทันที (ไม่ต้องติ๊ก/กดเอง)
            if (autoLoc) { try { sessionStorage.setItem('store_autoLoc', autoLoc); } catch (e) {} }
            uiToast(data.message || 'บันทึกเรียบร้อย', 'success');
            setTimeout(() => window.location.reload(), 500);
            return true;
        } else {
            uiToast(data.message || 'ดำเนินการไม่สำเร็จ', 'error');
            btn.disabled = false;
            return false;
        }
    } catch (e) {
        console.error(e);
        uiToast('เกิดข้อผิดพลาด', 'error');
        btn.disabled = false;
        return false;
    }
}
// ดรอปดาว "กำลังจัดการ" — เลือกชื่อ (โอ/ฟิว) แล้วบันทึกเข้า do_it
document.querySelectorAll('.claim-select').forEach(sel => {
    sel.addEventListener('change', async () => {
        const doBy = sel.value;
        if (!doBy) return;
        const row = sel.closest('tr');
        const ok = await postClaimAction(
            sel.dataset.type === 'legacy' ? LEGACY_CLAIM_URL : CLAIM_URL,
            sel.dataset.po, sel,
            {
                title: 'ยืนยันผู้จัดการงาน',
                message: 'ให้ "' + doBy + '" เป็นผู้จัดการงาน PO นี้ใช่หรือไม่',
                icon: IC_USER,
                detail: [['PO', row?.dataset.po || '-'], ['SO', row?.dataset.so || '-'], ['ผู้จัดการ', doBy]]
            },
            sel.dataset.type === 'legacy' ? 'store_id' : 'po_id',
            { do_by: doBy }
        );
        if (!ok) sel.value = '';   // ยกเลิก/ล้มเหลว -> รีเซ็ตดรอปดาว
    });
});
document.querySelectorAll('.btn-finish-claim').forEach(btn => {
    btn.addEventListener('click', () => {
        const autoLoc = btn.dataset.checkbox || null;   // ใบนี้เพื่อเปิดเลือกชั้นวางต่อทันทีหลังยืนยัน
        const row = btn.closest('tr');
        const opts = {
            title: 'คุณยืนยันที่จะจัดงานเสร็จสิ้นหรือไม่',
            message: 'ยืนยันแล้วจะเปิดให้เลือกชั้นวางต่อทันที',
            tone: 'success', okText: 'จัดการเสร็จสิ้น',
            detail: [['PO', row?.dataset.po || '-'], ['SO', row?.dataset.so || '-']]
        };
        if (btn.dataset.internal === '1') {
            postClaimAction(FINISH_INTERNAL_URL, btn.dataset.po, btn, opts, 'internal_id', {}, autoLoc);
        } else {
            postClaimAction(FINISH_URL, btn.dataset.po, btn, opts, 'po_id', {}, autoLoc);
        }
    });
});

const itemsModal     = document.getElementById('itemsModal');
const itemsModalTitle = document.getElementById('itemsModalTitle');
const itemsModalBody  = document.getElementById('itemsModalBody');

// ดึง SO จากชื่อสินค้าที่ฝัง "_SO69/008617" ไว้ (กรณีไม่มี field so มาให้)
function extractSoFromName(name){
    const m = String(name || '').match(/_SO\s*([0-9./-]+)/i);
    return m ? m[1] : '';
}
// ตัดส่วน "_SO..." ออกจากชื่อให้สะอาด (SO ไปแสดงในคอลัมน์ SO แทน)
function cleanItemName(name){
    return String(name || '').replace(/_SO\s*[0-9./-]+/ig, '').trim() || '-';
}
// normalize SO เพื่อเทียบ (ตัดช่องว่าง/คำนำหน้า SO)
function normSo(v){
    return String(v || '').replace(/\s+/g, '').replace(/^SO/i, '');
}
// SO ของ item: จาก field so/so_id ก่อน ถ้าไม่มีค่อยแกะจากชื่อ
function soOfItem(it){
    const rawName = it.name ?? it.item_name ?? '-';
    return (it.so ?? it.so_id ?? '') || extractSoFromName(rawName) || '';
}
// filterSo = so_id ของแถวที่กด → แสดงเฉพาะสินค้าของ PO+SO นั้น (PO เดียวกันมีได้หลาย SO)
function renderItemsTable(items, filterSo){
    if (!items || !items.length) {
        itemsModalBody.innerHTML = '<div class="items-modal-empty">ไม่พบรายการสินค้า</div>';
        return;
    }
    // กรองให้เหลือเฉพาะ SO ของแถวนี้ (ถ้าข้อมูลระบุ SO ได้)
    const want = normSo(filterSo);
    if (want) {
        const anyHasSo = items.some(it => normSo(soOfItem(it)) !== '');
        if (anyHasSo) {
            items = items.filter(it => normSo(soOfItem(it)) === want);
        }
    }
    if (!items.length) {
        itemsModalBody.innerHTML = '<div class="items-modal-empty">ไม่พบรายการสินค้าของ SO นี้</div>';
        return;
    }
    // จัดกลุ่มตาม SO เพื่อให้เห็นว่าแต่ละ SO มีสินค้าอะไรบ้าง
    const groups = {};
    const order  = [];
    items.forEach(it => {
        const rawName = it.name ?? it.item_name ?? '-';
        // SO ของรายการ: จากข้อมูล item ก่อน ถ้าไม่มีก็ใช้ SO ของแถวที่กด (modal เปิดตาม PO+SO อยู่แล้ว)
        const so = soOfItem(it) || filterSo || '';
        const key = so || 'ไม่ระบุ SO';
        if (!groups[key]) { groups[key] = []; order.push(key); }
        groups[key].push({ name: cleanItemName(rawName), qty: Number(it.qty ?? it.item_quantity ?? 0) });
    });

    const blocks = order.map(so => {
        const rows = groups[so].map(r => `
            <tr>
                <td class="nm">${esc(r.name)}</td>
                <td class="col-fit num">${r.qty.toFixed(2)}</td>
            </tr>`).join('');
        return `
            <div class="so-group">
                <div class="so-tag">SO ${esc(so)}</div>
                <table class="items-tbl">
                    <thead><tr><th style="text-align:left;">ชื่อสินค้า</th><th class="col-fit">จำนวน</th></tr></thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>`;
    }).join('');
    itemsModalBody.innerHTML = blocks;
}

document.querySelectorAll('.btn-view-items').forEach(btn => {
    btn.addEventListener('click', async () => {
        const po = btn.dataset.po;
        const so = btn.dataset.so || '';
        itemsModalTitle.textContent = 'รายการสินค้า — PO ' + po + (so ? ' / SO ' + so : '');
        itemsModal.showModal();

        // มี items ฝังไว้แล้ว (external/legacy/new) → แสดงเลย ไม่ต้อง fetch (กรองตาม SO ของแถวนี้)
        if (btn.dataset.items) {
            try { renderItemsTable(JSON.parse(btn.dataset.items), so); }
            catch (e) { itemsModalBody.innerHTML = '<div class="items-modal-empty">ข้อมูลสินค้าผิดพลาด</div>'; }
            return;
        }

        // ไม่มี → โหลดจาก server (ส่ง so ไปด้วยเพื่อกรองฝั่ง server ในอนาคต + กรองซ้ำฝั่ง client)
        itemsModalBody.innerHTML = '<div class="items-modal-loading"><div class="spin"></div>กำลังโหลด...</div>';
        try {
            const res = await fetch(LEGACY_ITEMS_URL + '?po=' + encodeURIComponent(po) + (so ? '&so=' + encodeURIComponent(so) : ''), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (res.ok && data.ok) renderItemsTable(data.items, so);
            else itemsModalBody.innerHTML = '<div class="items-modal-empty">โหลดไม่สำเร็จ</div>';
        } catch (e) {
            console.error(e);
            itemsModalBody.innerHTML = '<div class="items-modal-empty">เกิดข้อผิดพลาด</div>';
        }
    });
});
// กดพื้นหลังเพื่อปิดป๊อปอัพ
itemsModal.addEventListener('click', e => { if (e.target === itemsModal) itemsModal.close(); });

function setHint(text, isError) {
    const h = document.getElementById('dlgHint');
    h.textContent = text;
    if (isError) h.dataset.err = '1'; else delete h.dataset.err;
    h.style.color = isError ? 'var(--danger-dark)' : 'var(--ink)';
    h.style.borderLeftColor = isError ? 'var(--danger)' : 'var(--primary)';
    h.style.background = isError ? 'var(--danger-light)' : 'var(--primary-light)';
}

function openModal() {
    if (!currentUser())        { uiToast('กรุณาระบุชื่อผู้ดำเนินการ', 'error'); return; }
    if (!selectedIds().length) { uiToast('ยังไม่ได้เลือกรายการ', 'error'); return; }
    renderSelChips(); renderShelves();
    const p = document.getElementById('locModal');
    if (window.matchMedia('(max-width:1100px)').matches) {
        p.classList.add('open'); document.getElementById('lpBackdrop').classList.add('open');
    } else {
        p.classList.remove('flash'); void p.offsetWidth; p.classList.add('flash');
        setTimeout(() => p.classList.remove('flash'), 900);
        if (window.matchMedia('(pointer:fine)').matches) document.getElementById('inpLocation').focus({ preventScroll: true });
    }
}
function closePanel() {
    document.getElementById('locModal').classList.remove('open');
    document.getElementById('lpBackdrop').classList.remove('open');
}
document.addEventListener('keydown', e => { if (e.key === 'Escape' && document.getElementById('locModal').classList.contains('open')) closePanel(); });

async function confirmLoc() {
    const box = document.getElementById('inpLocation').value.trim();
    if (!box) {
        setHint('กรุณาระบุชั้นวาง', true);
        document.getElementById('inpLocation').focus();
        return;
    }
    if (!SHELF_OPTIONS.includes(box)) {
        setHint('ไม่พบชั้นวางนี้ในระบบ กรุณาเลือกจากรายการ', true);
        document.getElementById('inpLocation').focus();
        return;
    }


    const btn = document.querySelector('#locModal .lp-save');
    btn.disabled = true;
    btn.textContent = 'กำลังบันทึก...';
    const restore = () => { btn.disabled = false; updateSaveBtn(); };
    try {
        const res = await fetch(SUBMIT_URL, {
            method: 'POST',
            headers: { 'Content-Type':'application/json','Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF },
            body: JSON.stringify({ ids: selectedIds(), user: currentUser(), location: box })
        });
        const data = await res.json();
        if (res.ok && data.ok) {
            closePanel();
            uiToast(data.message || 'บันทึกตำแหน่งเรียบร้อย', 'success');
            setTimeout(() => window.location.reload(), 900);
        }
        else { setHint(data.message || 'บันทึกไม่สำเร็จ', true); uiToast(data.message || 'บันทึกไม่สำเร็จ', 'error'); restore(); }
    } catch (e) { console.error(e); setHint('เกิดข้อผิดพลาด', true); uiToast('เกิดข้อผิดพลาด', 'error'); restore(); }
}

// ===== Live Search =====
const searchAll = document.getElementById('searchAll');
const searchSO = document.getElementById('searchSO');
const searchPO = document.getElementById('searchPO');
const searchCustomer = document.getElementById('searchCustomer');
const filterPoType = document.getElementById('filterPoType');
const filterHandler = document.getElementById('filterHandler');
const btnClear = document.getElementById('btnClear');
let stageQ = '';

// เรียงใบ: พร้อมระบุตำแหน่ง -> กำลังจัดการ -> รอดำเนินการ -> ขึ้นชั้นแล้ว
(function sortRows(){
    const ORDER = { ready: 0, doing: 1, wait: 2, done: 3 };
    const body = document.getElementById('tableBody');
    const rows = Array.from(body.querySelectorAll('tr[data-stage]'));
    rows.map((r, i) => [r, i]).sort((a, b) => (ORDER[a[0].dataset.stage] - ORDER[b[0].dataset.stage]) || (a[1] - b[1]))
        .forEach(([r]) => body.insertBefore(r, body.querySelector('tr.no-match')));
})();


const filterStage = document.getElementById('filterStage');
filterStage.addEventListener('change', () => { stageQ = filterStage.value; liveFilter(); });

function liveFilter() {
    const allQ = searchAll.value.trim().toLowerCase();   // ช่องเดียว: ตรง SO หรือ PO หรือลูกค้า อย่างใดอย่างหนึ่ง
    const soQ = searchSO.value.trim().toLowerCase();
    const poQ = searchPO.value.trim().toLowerCase();
    const custQ = searchCustomer.value.trim().toLowerCase();
    const typeQ = filterPoType.value;
    const handlerQ = filterHandler.value;

    const rows = document.querySelectorAll('#tableBody tr[data-done]');
    let visibleCount = 0;
    let todoCount = 0;
    const stageCnt = { '': 0, ready: 0, doing: 0, wait: 0, done: 0 };

    rows.forEach(row => {
        const so = (row.dataset.so || '').toLowerCase();
        const po = (row.dataset.po || '').toLowerCase();
        const cust = (row.dataset.customer || '').toLowerCase();
        const type = row.dataset.poType || '';
        const handler = (row.dataset.handler || '').trim();

        const anyHit = !allQ || so.includes(allQ) || po.includes(allQ) || cust.includes(allQ);
        const matchSO = allQ ? anyHit : (!soQ || so.includes(soQ));
        const matchPO = allQ ? true : (!poQ || po.includes(poQ));
        const matchCust = allQ ? true : (!custQ || cust.includes(custQ));
        const matchType = !typeQ || type === typeQ;
        const matchHandler = !handlerQ || handler === handlerQ;

        const matchSearch = matchSO && matchPO && matchCust && matchType && matchHandler;
        if (matchSearch && row.dataset.done === '0') todoCount++;
        if (matchSearch) { stageCnt['']++; stageCnt[row.dataset.stage] = (stageCnt[row.dataset.stage] || 0) + 1; }
        if (matchSearch && (!stageQ || row.dataset.stage === stageQ)) {
            row.classList.remove('hidden-row');
            const chk = row.querySelector('.chkLine');
            if (chk) chk.disabled = (row.dataset.done === '1');
            visibleCount++;

        } else {
            row.classList.add('hidden-row');
            const chk = row.querySelector('.chkLine');
            if (chk) {
                chk.checked = false;
                chk.disabled = true;
            }
        }
    });

    document.getElementById('showCount').textContent = visibleCount;
    const noMatch = document.querySelector('#tableBody tr.no-match');
    if (noMatch) noMatch.hidden = !(rows.length && visibleCount === 0);
    document.getElementById('todoCount').textContent = todoCount;
    document.getElementById('todoBlock').classList.toggle('zero', todoCount === 0);
    document.getElementById('chkAll').checked = false;
    refreshBtn();
    syncDropdowns();
}

// ===== ดรอปดาวน์สวย ๆ ครอบ select เดิม (เลือกแล้วตั้ง .value + ยิง change เหมือนเลือกจาก select ปกติ) =====
const DD_DOTS = {
    filterPoType:  { '': '#94a3b8', internal: '#1a4fd6', external: '#d97706' },
    filterHandler: { '': '#94a3b8', 'โอ': '#16a34a', 'ฟิว': '#7c3aed' },
    filterStage:   { '': '#94a3b8', ready: '#1a4fd6', doing: '#d97706', wait: '#475569', done: '#16a34a' }
};
const DD_COUNT = {   // จำนวนใบในหน้านี้ของแต่ละตัวเลือก
    filterPoType:  v => document.querySelectorAll('#tableBody tr[data-done]' + (v ? '[data-po-type="' + v + '"]' : '')).length,
    filterHandler: v => document.querySelectorAll('#tableBody tr[data-done]' + (v ? '[data-handler="' + v + '"]' : '')).length,
    filterStage:   v => document.querySelectorAll('#tableBody tr[data-done]' + (v ? '[data-stage="' + v + '"]' : '')).length
};
const dropdowns = [];
function initDropdown(box) {
    const sel = box.querySelector('select'), btn = box.querySelector('.dd-btn'), panel = box.querySelector('.dd-panel'), txt = box.querySelector('.dd-text'), dot = box.querySelector('.st-dot');
    let hl = -1;
    const opts = () => Array.from(sel.options);
    const colorOf = v => (DD_DOTS[sel.id] || {})[v] || '#94a3b8';
    function sync() {
        const o = sel.options[sel.selectedIndex];
        txt.textContent = o ? o.textContent : '';
        dot.style.background = colorOf(sel.value);
        dot.style.boxShadow = sel.value ? '0 0 0 3px ' + colorOf(sel.value) + '22' : 'none';
    }
    function render() {
        panel.innerHTML = opts().map((o, i) =>
            '<div class="dd-item' + (o.value === sel.value ? ' sel' : '') + (i === hl ? ' hl' : '') + '" role="option" data-i="' + i + '">' +
            '<span class="d" style="background:' + colorOf(o.value) + '"></span>' + esc(o.textContent) +
            '<span class="n">' + (DD_COUNT[sel.id] ? DD_COUNT[sel.id](o.value) : '') + '</span>' +
            '<svg class="i ck" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg></div>').join('');
    }
    function open()  { dropdowns.forEach(d => d !== api && d.close()); hl = sel.selectedIndex; render(); box.classList.add('open'); }
    function close() { box.classList.remove('open'); }
    function pick(i) {
        if (sel.selectedIndex !== i) { sel.selectedIndex = i; sel.dispatchEvent(new Event('change', { bubbles: true })); }
        sync(); close(); btn.focus();
    }
    box.addEventListener('click', e => {
        const it = e.target.closest('.dd-item');
        if (it) { pick(+it.dataset.i); return; }
        if (e.target.closest('.dd-panel')) return;
        box.classList.contains('open') ? close() : open();
    });
    btn.addEventListener('keydown', e => {
        const n = sel.options.length;
        if (!box.classList.contains('open')) {
            if (['ArrowDown', 'ArrowUp', 'Enter', ' '].includes(e.key)) { e.preventDefault(); open(); }
            return;
        }
        if (e.key === 'ArrowDown') { e.preventDefault(); hl = (hl + 1) % n; render(); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); hl = (hl - 1 + n) % n; render(); }
        else if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); if (hl >= 0) pick(hl); }
        else if (e.key === 'Escape' || e.key === 'Tab') { close(); }
    });
    sel.addEventListener('change', sync);
    const api = { sync, close };
    dropdowns.push(api);
    sync();
    return api;
}
function syncDropdowns() { dropdowns.forEach(d => d.sync()); }
initDropdown(document.getElementById('ddPoType'));
// เติมรายชื่อผู้จัดการใน "filter" แบบ dynamic จากชื่อที่มีจริงในรายการ (รวมชื่อจาก box)
// — ส่วน select "เลือกจัดการ" (claim-select) ยังคงมีแค่ โอ/ฟิว เหมือนเดิม ไม่แตะ
(function populateHandlerFilter(){
    const sel = document.getElementById('filterHandler');
    if(!sel) return;
    const existing = new Set(Array.from(sel.options).map(o => o.value));
    const names = new Set();
    document.querySelectorAll('#tableBody tr[data-handler]').forEach(tr => {
        const h = (tr.dataset.handler || '').trim();
        if(h) names.add(h);
    });
    Array.from(names).sort((a,b)=>a.localeCompare(b,'th')).forEach(n => {
        if(!existing.has(n)){
            const o = document.createElement('option');
            o.value = n; o.textContent = n;
            sel.appendChild(o);
        }
    });
})();
initDropdown(document.getElementById('ddHandler'));
initDropdown(document.getElementById('ddStage'));
document.addEventListener('click', e => { if (!e.target.closest('.dd')) dropdowns.forEach(d => d.close()); });

// แยกให้เองว่าคำที่พิมพ์เป็น SO / PO / ลูกค้า (ใช้ตอนกดค้นหาทุกหน้า)
function splitQuery() {
    const q = searchAll.value.trim();
    searchSO.value = searchPO.value = searchCustomer.value = '';
    if (!q) return;
    if (/^po/i.test(q) || q.includes('-') || /^A\d/i.test(q))       searchPO.value = q;        // PO เช่น PO6909-02397, 6910-A0092
    else if (/^so/i.test(q) || q.includes('/'))                      searchSO.value = q;        // SO เช่น 69/017868
    else if (/[a-zA-Z\u0E00-\u0E7F]/.test(q))                       searchCustomer.value = q;  // มีตัวอักษร = ชื่อลูกค้า
    else                                                             searchSO.value = q;        // ตัวเลขล้วน = SO
}
searchAll.addEventListener('input', () => { splitQuery(); liveFilter(); });
searchSO.addEventListener('input', liveFilter);
searchPO.addEventListener('input', liveFilter);
searchCustomer.addEventListener('input', liveFilter);
filterPoType.addEventListener('change', liveFilter);
filterHandler.addEventListener('change', liveFilter);

btnClear.addEventListener('click', () => {
    searchAll.value = '';
    searchSO.value = '';
    searchPO.value = '';
    searchCustomer.value = '';
    filterPoType.value = '';
    filterHandler.value = '';
    stageQ = '';
    filterStage.value = '';
    liveFilter();
});

// ===== ปุ่มค้นหา: ค้นจากฝั่ง server ทุกหน้า (SONum / PONum / customer / po_type) — ระหว่างพิมพ์ยังกรองในหน้านี้ทันทีเหมือนเดิม =====
const btnSearch = document.getElementById('btnSearch');
function runSearch() {
    const u = new URL(window.location.href);
    const setQ = (k, v) => { v = (v || '').trim(); if (v) u.searchParams.set(k, v); else u.searchParams.delete(k); };
    if (searchAll.value.trim()) splitQuery();
    setQ('SONum', searchSO.value);
    setQ('PONum', searchPO.value);
    setQ('customer', searchCustomer.value);
    setQ('po_type', filterPoType.value);
    u.searchParams.delete('page');          // ค้นใหม่ = เริ่มหน้า 1
    btnSearch.classList.add('loading');
    btnSearch.disabled = true;
    window.location.href = u.toString();
}
btnSearch.addEventListener('click', runSearch);
[searchAll].forEach(inp => inp.addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); runSearch(); }
}));

// แผงขึ้นชั้น: ติดใต้แถบบนพอดี (คำนวณจากความสูงแถบบนจริง -> เริ่มเท่ากับตาราง)
function syncStickTop() {
    const bn = document.querySelector('.top-banner');
    const top = getComputedStyle(bn).position === 'sticky' ? bn.offsetHeight + 10 + 12 : 12;
    document.documentElement.style.setProperty('--stick-top', top + 'px');
}
syncStickTop();
window.addEventListener('resize', syncStickTop);

renderZones();
renderShelves();
liveFilter();

// หลังกด "จัดการเสร็จสิ้น" + ยืนยัน -> รีโหลดแล้วเปิดเลือกชั้นวางของใบนั้นให้เลย (ไม่ต้องมาติ๊ก/กดเลือกเอง)
(function autoOpenLocation(){
    let val = null;
    try { val = sessionStorage.getItem('store_autoLoc'); sessionStorage.removeItem('store_autoLoc'); } catch (e) {}
    if (!val) return;
    const chk = document.querySelector('.chkLine[value="' + val.replace(/"/g, '\\"') + '"]');
    if (!chk || chk.disabled) return;
    // เหลือเลือกเฉพาะใบนี้ใบเดียว แล้วเปิด modal ให้เลือกชั้นวางทันที
    document.querySelectorAll('.chkLine:checked').forEach(c => { if (c !== chk) c.checked = false; });
    chk.checked = true;
    refreshBtn();
    openModal();
})();
</script>
</body>
</html>