<!DOCTYPE html>
{{-- resources/views/store/store_checkout.blade.php — ธีมเดียวกับหน้า ชั้น SALE / จัดบิลขึ้นชั้น --}}
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>จัดบิลส่งของ</title>
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
    html,body{background:var(--page-bg);font-family:'Sarabun','Segoe UI',Tahoma,sans-serif;color:var(--ink);line-height:1.5;-webkit-font-smoothing:antialiased;overflow-x:hidden;max-width:100%}
    body{min-height:100vh;padding-bottom:40px}
    body.has-floatbar{padding-bottom:110px}
    button,input,select{font:inherit}
    button{cursor:pointer}
    svg.i{width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;flex:none}

    /* ===== แถบบน (แบบเดียวกับหน้าอื่น) ===== */
    .top-banner{display:flex;align-items:center;gap:14px;flex-wrap:wrap;background:#fff;border:1px solid var(--line);border-radius:var(--r-card);
                margin:15px 15px 14px;padding:12px 16px 12px 18px;box-shadow:var(--shadow);position:sticky;top:10px;z-index:100}
    .top-banner .h1{flex:none;display:flex;align-items:center;gap:10px;font-weight:800;font-size:clamp(19px,.8vw + 10px,23px);letter-spacing:-.3px;white-space:nowrap;color:var(--ink);text-decoration:none}
    .top-banner .logo{width:38px;height:38px;border-radius:10px;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 10px rgba(26,79,214,.28)}
    .top-banner .logo svg{width:20px;height:20px}
    .tools{flex:1 1 auto;display:flex;align-items:center;gap:8px;min-width:0}
    .tools form{display:flex;align-items:center;gap:8px;flex:0 1 auto;min-width:0;flex-wrap:nowrap}
    .tools form .fld.f-all{width:420px}
    .banner-right{display:flex;align-items:center;gap:8px;margin-left:auto;flex-shrink:0}
    .fld{height:40px;display:flex;align-items:center;gap:8px;padding:0 12px;background:var(--page-bg);border:1px solid var(--line);border-radius:var(--r-field);transition:.15s;min-width:0;flex:1 1 170px;position:relative}
    .fld:hover{border-color:var(--border)}
    .fld:focus-within{background:#fff;border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light)}
    .fld > svg{color:var(--faint)}
    .fld input{border:0;outline:0;background:transparent;font-size:.95rem;color:var(--ink);height:100%;min-width:0;padding:0;flex:1;width:100%}
    .fld input::placeholder{color:var(--faint)}
    .fld .lbl{font-size:.8rem;font-weight:600;color:var(--muted);white-space:nowrap}
    /* ช่องค้นหาเดียว (SO / PO / บิล) */
    .fld.f-all{flex:1 1 260px;min-width:220px;max-width:420px}
    .qdetect{flex:none;height:24px;display:inline-flex;align-items:center;gap:4px;padding:0 9px;border:0;border-radius:999px;background:var(--soft);color:var(--muted);font-size:.74rem;font-weight:700;white-space:nowrap;transition:.15s}
    .qdetect[hidden]{display:none}
    .qdetect:hover{background:var(--primary-light);color:var(--primary)}
    .qdetect.manual{background:var(--primary-light);color:var(--primary)}
    .vsep{width:1px;height:28px;background:var(--line);flex:none}
    .tools form .fld.f-date{flex:0 0 250px}
    .fld.f-date{flex:0 1 270px}
    .fld.f-date input{font-weight:600;cursor:pointer}
    /* บังคับช่องวันที่ให้แสดงรูปแบบ วัน/เดือน/ปี */
    input[type="date"]{appearance:none;-webkit-appearance:none}
    input[type="date"]::-webkit-datetime-edit-fields-wrapper{display:flex}
    input[type="date"]::-webkit-datetime-edit-text{padding:0 2px;color:var(--muted)}
    input[type="date"]::-webkit-calendar-picker-indicator{opacity:.55;cursor:pointer}
    .btn-icon{width:40px;height:40px;flex:none;display:inline-flex;align-items:center;justify-content:center;border:1px solid var(--line);border-radius:var(--r-field);background:#fff;color:var(--muted);text-decoration:none;transition:.15s}
    .btn-icon svg{width:17px;height:17px}
    .btn-icon:hover{background:var(--soft);color:var(--ink);border-color:var(--border)}
    .btn-icon.reset:hover{color:var(--danger);border-color:#fecaca;background:#fef2f2}
    .search-btn{height:40px;flex:none;display:inline-flex;align-items:center;gap:8px;padding:0 12px 0 16px;border:0;border-radius:var(--r-field);background:var(--primary);color:#fff;
                font-size:1.02rem;font-weight:700;white-space:nowrap;box-shadow:0 1px 0 rgba(255,255,255,.22) inset,0 3px 10px rgba(26,79,214,.20);transition:transform .12s,background .15s}
    .search-btn:hover{background:var(--primary-dark);transform:translateY(-1px)}
    .search-btn .sb-ic{display:inline-flex}
    .search-btn .sb-ic svg{width:19px;height:19px}
    .search-btn .sb-kbd{font:inherit;font-size:.76rem;font-weight:600;color:rgba(255,255,255,.9);background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.28);border-radius:6px;padding:1px 6px;line-height:18px}
    .search-btn .sb-spin{display:none;width:16px;height:16px;border-radius:50%;border:2px solid rgba(255,255,255,.35);border-top-color:#fff;animation:spin .7s linear infinite}
    .search-btn.loading .sb-ic{display:none}
    .search-btn.loading .sb-spin{display:inline-block}
    @keyframes spin{to{transform:rotate(360deg)}}
    /* บิลยังค้างอยู่ / ดูทั้งหมด */
    .list-meta{display:flex;align-items:center;flex:none;margin-left:auto}
    .filter-pills{display:inline-flex;gap:4px;background:var(--soft);padding:4px;border-radius:10px;border:1px solid var(--line);height:40px;align-items:center}
    .filter-pill{display:inline-flex;align-items:center;gap:7px;height:30px;padding:0 13px;border-radius:7px;text-decoration:none;color:var(--muted);font-size:.9rem;font-weight:700;white-space:nowrap;transition:.15s}
    .filter-pill:hover{color:var(--ink)}
    .filter-pill.active{background:#fff;color:var(--primary);box-shadow:0 1px 3px rgba(15,23,42,.1)}
    .filter-pill .d{width:7px;height:7px;border-radius:50%;background:currentColor}
    .user-badge{height:40px;display:inline-flex;align-items:center;gap:8px;padding:0 12px 0 5px;border-radius:999px;background:var(--page-bg);border:1px solid var(--line);font-size:.86rem;color:var(--ink-700);white-space:nowrap}
    .user-badge .av{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:var(--soft);color:var(--ink)}
    .user-badge .av svg{width:15px;height:15px}

    main{padding:0 15px 24px;width:100%}

    .empty-state{display:flex;flex-direction:column;align-items:center;gap:10px;text-align:center;padding:70px 20px;color:var(--muted);background:#fff;border:1px dashed var(--border);border-radius:var(--r-card);font-size:.98rem;font-weight:500}
    .empty-state svg{width:44px;height:44px;color:var(--border);stroke-width:1.5}

    /* ===== การ์ด SO ===== */
    .so-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;align-items:start}
    .so-card{background:#fff;border:1px solid var(--line);border-left:4px solid var(--warning);border-radius:var(--r-card);overflow:hidden;display:flex;flex-direction:column;min-width:0;box-shadow:var(--shadow);transition:box-shadow .2s,border-color .2s}
    .so-card[data-done="1"]{border-left-color:var(--success)}
    .so-card:hover{box-shadow:0 2px 6px rgba(15,23,42,.06),0 8px 20px rgba(15,23,42,.07)}
    .so-card:not(.collapsed){border-color:#c7d6fb;border-left-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light),var(--shadow)}
    .so-card-header{display:flex;align-items:flex-start;gap:12px;padding:14px 16px;cursor:pointer;transition:background .15s;outline:0}
    .so-card-header:hover,.so-card-header:focus-visible{background:#fafbfd}
    .so-toggle{width:26px;height:26px;margin-top:1px;border-radius:7px;flex:none;display:inline-flex;align-items:center;justify-content:center;background:var(--soft);color:var(--muted);font-size:0;transition:transform .2s,background .2s,color .2s;pointer-events:none}
    .so-toggle svg{width:15px;height:15px;stroke-width:2.6}
    .so-card:not(.collapsed) .so-toggle{transform:rotate(90deg);background:var(--primary);color:#fff}
    .so-card.collapsed .so-body{display:none}
    .so-head-main{flex:1;min-width:0}
    .so-id{font-weight:800;font-size:1.08rem;word-break:break-all;color:var(--ink);line-height:1.3}
    .so-id a{color:var(--primary);text-decoration:none}
    .so-id a:hover{text-decoration:underline}
    .so-id.is-done a{color:var(--success-dark)}
    .so-billno-row{display:flex;flex-wrap:wrap;align-items:center;gap:5px;margin-top:7px}
    .so-billno-count{font-size:.74rem;font-weight:800;color:var(--ink-700);background:var(--soft);padding:2px 8px;border-radius:999px;white-space:nowrap}
    .so-billno-chip{font-size:.78rem;font-weight:700;font-variant-numeric:tabular-nums;color:var(--warning-dark);background:var(--warning-light);padding:2px 8px;border-radius:6px;word-break:break-all}
    .so-billno-chip.is-picked{color:var(--success-dark);background:var(--success-light)}
    .so-billno-chip.is-cancelled{color:var(--danger);background:var(--danger-light);text-decoration:line-through}
    .so-sub{font-size:.86rem;color:var(--muted);margin-top:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .so-sub b{color:var(--ink-700);font-weight:700}
    .so-status{flex:none;display:inline-flex;align-items:center;gap:6px;font-size:.78rem;font-weight:700;padding:3px 11px;border-radius:999px;white-space:nowrap}
    .so-status::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
    .so-status.pending{background:var(--warning-light);color:var(--warning-dark)}
    .so-status.done{background:var(--success-light);color:var(--success-dark)}
    .so-body{border-top:1px solid var(--line)}

    /* ===== ส่วนบิล (DN) ===== */
    .dn-section{border-top:1px solid var(--line)}
    .dn-section:first-child{border-top:0}
    .dn-section-header{display:flex;align-items:center;gap:8px 10px;flex-wrap:wrap;padding:11px 16px;background:var(--page-bg)}
    .dn-no{font-weight:800;font-size:.98rem;font-variant-numeric:tabular-nums}
    .dn-no.is-done{color:var(--success-dark)}
    .dn-no.is-cancelled{color:var(--danger);text-decoration:line-through}
    .dn-time{font-size:.8rem;color:var(--muted)}
    .dnSelectAll,.chkPickOnly,.chkBill,.chkGroup{width:19px;height:19px;accent-color:var(--primary);flex:none;cursor:pointer}
    .chkBill{accent-color:var(--success)}
    .dn-check-label{display:inline-flex;align-items:center;gap:7px;height:32px;padding:0 12px 0 9px;border:1px solid #bbf7d0;border-radius:8px;background:#fff;cursor:pointer;font-size:.84rem;font-weight:700;color:var(--success-dark);white-space:nowrap;transition:.15s}
    .dn-check-label:hover{background:var(--success-light)}
    .dn-check-label:has(.chkBill:checked){background:var(--success);border-color:var(--success);color:#fff}
    .dn-check-label:has(.chkBill:checked) .chkBill{accent-color:#fff}
    .dn-cancelled-badge,.dn-picked-badge{margin-left:auto;font-size:.78rem;font-weight:700;white-space:nowrap;padding:3px 10px;border-radius:999px}
    .dn-cancelled-badge{color:var(--danger-dark);background:var(--danger-light)}
    .dn-picked-badge{color:var(--success-dark);background:var(--success-light);white-space:normal}
    .dn-section.dn-cancelled > .dn-section-header{background:#fff7f7}
    .dn-body{padding:12px 16px 16px}
    /* ได้รับบิลแล้ว */
    .bill-received-label{display:inline-flex;align-items:center;gap:6px;height:28px;padding:0 10px 0 8px;border:1px dashed var(--border);border-radius:999px;background:#fff;cursor:pointer;font-size:.78rem;font-weight:700;color:var(--muted);white-space:nowrap;transition:.15s}
    .bill-received-label:hover{border-color:var(--faint);color:var(--ink-700)}
    .bill-received-label input{width:15px;height:15px;cursor:pointer;accent-color:var(--success)}
    .bill-received-label.is-received{color:var(--success-dark);border-style:solid;border-color:#bbf7d0;background:var(--success-light)}
    .no-dn-note{font-size:.88rem;color:var(--muted);padding:14px;text-align:center;background:var(--page-bg)}

    /* ===== PO / สินค้า ===== */
    .po-row{border:1px solid var(--line);border-radius:10px;margin-bottom:10px;background:#fff;overflow:hidden}
    .po-row:last-of-type{margin-bottom:0}
    .po-row:has(.chkGroup:checked){border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light)}
    .po-row.po-row-done{background:#fbfefc;border-color:#d1f2dc}
    .po-row-head{display:flex;align-items:center;gap:8px;padding:10px 12px;flex-wrap:wrap;background:#fafbfd;border-bottom:1px solid var(--line)}
    .po-row-head:has(.chkGroup){cursor:pointer}
    .po-row.po-row-done .po-row-head{background:var(--success-light);border-bottom-color:#d1f2dc}
    .source-tag{font-size:.72rem;font-weight:700;color:var(--ink-700);padding:1px 8px;border-radius:999px;background:var(--soft);white-space:nowrap}
    .source-tag.t-int{background:var(--primary-light);color:var(--primary)}
    .source-tag.t-ext{background:var(--warning-light);color:var(--warning-dark)}
    .source-tag.t-round{background:#ede9fe;color:#6d28d9}
    .po-num{font-weight:800;font-size:.95rem;color:var(--ink)}
    .po-done-tag{margin-left:auto;display:inline-flex;align-items:center;gap:4px;font-size:.76rem;font-weight:700;color:var(--success-dark)}
    .po-done-tag svg{width:13px;height:13px;stroke-width:3}
    .item-col-head{display:flex;justify-content:space-between;gap:10px;padding:7px 12px 5px;font-size:.72rem;color:var(--faint);font-weight:700}
    .item-row{display:flex;justify-content:space-between;align-items:baseline;gap:10px;padding:6px 12px;border-top:1px dashed var(--line)}
    .item-col-head + .item-row{border-top:0}
    .item-row .item-name{font-size:.9rem;color:var(--ink);font-weight:500;min-width:0}
    .item-row .item-qty{font-size:.92rem;font-weight:800;color:var(--ink);white-space:nowrap;font-variant-numeric:tabular-nums}
    .item-move-row{display:flex;align-items:center;justify-content:space-between;gap:8px;padding:0 12px 8px}
    .item-move-shelf{display:inline-flex;align-items:center;gap:4px;font-size:.78rem;color:var(--muted);font-weight:600}
    .item-move-shelf svg{width:12px;height:12px}
    .po-row-meta,.item-row-meta{display:flex;align-items:center;gap:6px;flex-wrap:wrap;font-size:.8rem;color:var(--muted);font-weight:500;padding:6px 12px;border-top:1px solid var(--line);background:#fcfcfd}
    .po-row-meta svg,.item-row-meta svg{width:13px;height:13px;color:var(--faint)}
    .po-row-meta b,.item-row-meta b{color:var(--ink-700)}
    .po-row-meta.checkout-meta{color:var(--success-dark);font-weight:700;background:var(--success-light);border-top-color:#d1f2dc}
    .po-row-meta.checkout-meta svg{color:var(--success)}
    .po-row-meta.meta-actions{background:#fff;padding:8px 12px}
    .btn-move-shelf,.btn-move-line{display:inline-flex;align-items:center;gap:6px;border:1px solid #c7d6fb;color:var(--primary);background:#fff;border-radius:8px;font-weight:700;white-space:nowrap;transition:.15s}
    .btn-move-shelf{height:32px;padding:0 12px;font-size:.82rem}
    .btn-move-line{height:26px;padding:0 9px;font-size:.74rem}
    .btn-move-shelf svg,.btn-move-line svg{width:13px;height:13px}
    .btn-move-shelf:hover,.btn-move-line:hover{background:var(--primary-light);border-color:var(--primary)}

    /* ===== แถบลอย (เลือกแล้ว / บันทึก) ===== */
    @keyframes floatIn{from{opacity:0;transform:translate(-50%,16px)}to{opacity:1;transform:translate(-50%,0)}}
    .checkout-floatbar{position:fixed;left:50%;bottom:22px;transform:translateX(-50%);z-index:150;display:flex;align-items:center;gap:10px;padding:8px 8px 8px 20px;
                       background:#fff;border:1px solid var(--line);border-radius:999px;box-shadow:0 12px 36px rgba(15,23,42,.22);animation:floatIn .18s ease-out;white-space:nowrap}
    .checkout-floatbar[hidden]{display:none}
    .checkout-floatbar .floatbar-count{font-size:.95rem;font-weight:600;color:var(--ink-700)}
    .checkout-floatbar .floatbar-count strong{display:inline-flex;align-items:center;justify-content:center;min-width:28px;height:28px;padding:0 8px;margin:0 3px;border-radius:999px;background:var(--primary);color:#fff;font-size:.95rem;font-variant-numeric:tabular-nums}
    .checkout-floatbar .btn-ghost{height:42px;border-radius:999px}
    .checkout-floatbar .btn-success{height:42px;padding:0 22px;border-radius:999px;display:inline-flex;align-items:center;gap:7px;font-size:1rem}
    .checkout-floatbar .btn-success svg{width:17px;height:17px;stroke-width:2.6}

    /* ===== ปุ่มทั่วไป ===== */
    .btn-ghost{height:42px;padding:0 18px;border:1px solid var(--line);border-radius:var(--r-field);background:#fff;color:var(--ink-700);font-weight:600}
    .btn-ghost:hover{background:var(--soft)}
    .btn-success{height:42px;padding:0 20px;border:0;border-radius:var(--r-field);background:var(--success);color:#fff;font-weight:700;box-shadow:0 3px 10px rgba(22,163,74,.22)}
    .btn-success:hover{background:var(--success-dark)}
    .btn-primary{height:42px;padding:0 20px;border:0;border-radius:var(--r-field);background:var(--primary);color:#fff;font-weight:700}
    .btn-primary:hover{background:var(--primary-dark)}
    button:disabled{opacity:.55;cursor:not-allowed}

    /* ===== แบ่งหน้า ===== */
    .pager{display:flex;gap:6px;align-items:center;flex-wrap:wrap;justify-content:center;margin-top:22px}
    .pager a,.pager span{display:inline-flex;align-items:center;justify-content:center;min-width:40px;height:38px;padding:0 12px;border:1px solid var(--line);border-radius:999px;font-size:.88rem;font-weight:700;text-decoration:none;color:var(--ink-700);background:#fff;box-shadow:var(--shadow);transition:.15s}
    .pager a:hover{border-color:var(--primary);color:var(--primary)}
    .pager span.current{background:var(--primary);color:#fff;border-color:var(--primary)}
    .pager span.disabled{color:var(--faint);background:var(--page-bg);box-shadow:none;cursor:not-allowed}

    /* ===== ป๊อปอัพ ===== */
    .modal-overlay{position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:300;align-items:center;justify-content:center;padding:20px}
    .modal-box{background:#fff;border-radius:16px;padding:22px;width:min(92vw,420px);box-shadow:0 24px 60px rgba(0,0,0,.28);animation:modalPop .15s ease-out}
    @keyframes modalPop{from{opacity:0;transform:translateY(8px) scale(.98)}to{opacity:1;transform:none}}
    .modal-head{display:flex;align-items:center;gap:12px;margin-bottom:16px}
    .modal-icon{width:42px;height:42px;border-radius:10px;flex:none;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center}
    .modal-icon svg{width:20px;height:20px}
    .modal-title{font-weight:700;font-size:1.1rem;line-height:1.3}
    .modal-sub{margin-top:2px;font-size:.86rem;color:var(--muted);font-weight:500;word-break:break-word}
    .tp-list{display:flex;flex-direction:column;gap:10px}
    .tp-opt{display:flex;align-items:center;gap:14px;padding:14px 16px;border:1.5px solid var(--line);border-radius:12px;background:#fff;text-align:left;transition:.15s}
    .tp-opt .tp-ic{width:44px;height:44px;border-radius:11px;display:flex;align-items:center;justify-content:center;flex:none}
    .tp-opt .tp-ic svg{width:22px;height:22px}
    .tp-opt b{display:block;font-size:1rem;color:var(--ink)}
    .tp-opt small{display:block;font-size:.8rem;color:var(--muted)}
    .tp-opt .go{margin-left:auto;color:var(--faint)}
    .tp-opt.t-company .tp-ic{background:var(--primary-light);color:var(--primary)}
    .tp-opt.t-company:hover{border-color:var(--primary);background:#f7f9ff}
    .tp-opt.t-private .tp-ic{background:var(--success-light);color:var(--success-dark)}
    .tp-opt.t-private:hover{border-color:var(--success);background:#f6fdf8}
    .tp-cancel{margin-top:4px;width:100%}
    /* ย้ายชั้นวาง */
    .ms-field{position:relative}
    .ms-field > svg{position:absolute;left:13px;top:50%;transform:translateY(-50%);width:17px;height:17px;color:var(--faint);pointer-events:none}
    #msShelf{width:100%;height:46px;padding:0 14px 0 40px;border:1px solid var(--line);border-radius:var(--r-field);background:var(--page-bg);font-size:1rem;font-weight:600;outline:0;transition:.15s}
    #msShelf:focus{background:#fff;border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-light)}
    .ms-shelf-list{display:none;position:absolute;left:0;right:0;top:calc(100% + 6px);background:#fff;border:1px solid var(--border);border-radius:12px;box-shadow:0 12px 32px rgba(15,23,42,.16);max-height:300px;overflow-y:auto;z-index:10;padding:6px}
    .ms-shelf-opt{padding:10px 12px;font-size:.98rem;font-weight:600;color:var(--ink);border-radius:8px;cursor:pointer;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .ms-shelf-opt:hover{background:var(--primary-light);color:var(--primary)}
    .modal-actions{display:flex;gap:10px;justify-content:flex-end;margin-top:16px}

    /* ===== กล่องยืนยัน / แจ้งเตือน (แทน confirm / alert) ===== */
    .ui-dlg-overlay{position:fixed;inset:0;z-index:400;background:rgba(15,23,42,.5);display:flex;align-items:center;justify-content:center;padding:20px;opacity:0;visibility:hidden;transition:opacity .15s,visibility .15s}
    .ui-dlg-overlay.open{opacity:1;visibility:visible}
    .ui-dlg{width:min(92vw,420px);background:#fff;border-radius:16px;box-shadow:0 24px 60px rgba(0,0,0,.28);padding:26px 24px 20px;text-align:center;transform:translateY(8px) scale(.97);transition:transform .15s}
    .ui-dlg-overlay.open .ui-dlg{transform:none}
    .ui-dlg-icon{width:56px;height:56px;border-radius:50%;margin:0 auto 14px;display:flex;align-items:center;justify-content:center;background:var(--primary);color:#fff;box-shadow:0 0 0 6px var(--primary-light)}
    .ui-dlg-icon svg{width:26px;height:26px;stroke-width:2.4}
    .ui-dlg.t-success .ui-dlg-icon{background:var(--success);box-shadow:0 0 0 6px var(--success-light)}
    .ui-dlg.t-warn .ui-dlg-icon{background:var(--warning);box-shadow:0 0 0 6px var(--warning-light)}
    .ui-dlg-title{font-size:1.2rem;font-weight:700}
    .ui-dlg-msg{margin-top:6px;color:var(--muted);font-size:.95rem}
    .ui-dlg-msg:empty{display:none}
    .ui-dlg-actions{display:flex;gap:10px;margin-top:20px}
    .ui-dlg-actions button{flex:1}
    .ui-dlg.t-success #uiDlgOk{background:var(--success)}
    .ui-dlg.t-warn #uiDlgOk{background:var(--warning)}
    .ui-toast{position:fixed;left:50%;bottom:24px;z-index:450;transform:translate(-50%,20px);opacity:0;pointer-events:none;transition:.2s;width:max-content;max-width:92vw;
              background:var(--ink);color:#fff;padding:12px 18px;border-radius:12px;font-size:.92rem;font-weight:500;box-shadow:0 10px 30px rgba(0,0,0,.25)}
    .ui-toast.show{opacity:1;transform:translate(-50%,0)}
    .ui-toast.t-error{background:var(--danger)}
    .ui-toast.t-success{background:var(--success)}
    body.has-floatbar .ui-toast{bottom:96px}

    @media (max-width:1500px){ .tools{order:3;flex-basis:100%;flex-wrap:wrap} .vsep{display:none} .list-meta{margin-left:auto} }
    @media (max-width:1100px){ .tools form{flex:1 1 100%} .tools form .fld.f-all{width:auto;max-width:none} }
    @media (max-width:1400px){ .so-grid{grid-template-columns:repeat(2,minmax(0,1fr))} }
    @media (max-width:1000px){ .top-banner{position:static} }
    @media (max-width:800px){
        .so-grid{grid-template-columns:1fr}
        .tools .fld{flex:1 1 calc(50% - 4px)}
        .fld.f-all{flex:1 1 100%;max-width:none}
        .tools form .fld.f-all{width:auto}
        .tools{flex-wrap:wrap}
        .tools form{flex:1 1 100%;flex-wrap:wrap}
        .fld.f-date{flex:1 1 100%}
        .search-btn{flex:1 1 auto;justify-content:center}
        .list-meta,.filter-pills{width:100%}
        .filter-pill{flex:1;justify-content:center}
    }
    @media (max-width:560px){
        .user-badge .name{display:none}
        .tools .fld{flex-basis:100%}
        .search-btn .sb-kbd{display:none}
        .checkout-floatbar{left:12px;right:12px;transform:none;animation:none;padding-left:16px}
        .checkout-floatbar .floatbar-count{flex:1}
        .checkout-floatbar .btn-ghost{padding:0 12px}
    }
</style>
</head>
<body lang="th">

<div class="top-banner">
    <a class="h1" href="" title="รีเฟรชหน้า"><span class="logo"><svg class="i" viewBox="0 0 24 24"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.62l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg></span>จัดบิลส่งของ</a>
    <span class="vsep"></span>


    <div class="tools toolbar-container">
        <form class="toolbar" id="filterForm" method="GET" action="{{ url()->current() }}">
            <input type="hidden" name="filter_status" value="{{ request('filter_status', 'pending') }}">

            {{-- ช่องค้นหาเดียว: SO / PO / เลขบิล (ช่องเดิม 3 ช่องยังอยู่แบบซ่อน name เดิม — ตอนกดค้นหาจะใส่ค่าให้ช่องที่ตรงประเภท) --}}
            @php
                $qInit = request('SONum') ?: (request('PONum') ?: request('BillNo'));
                $qMode = request('SONum') ? 'so' : (request('PONum') ? 'po' : (request('BillNo') ? 'bill' : 'auto'));
            @endphp
            <div class="fld f-all" title="ค้นหาเลข SO / เลข PO / เลขบิล">
                <svg class="i" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                {{-- ประเภทการค้นหา: auto = เดาจากที่พิมพ์ / กดป้าย "ค้นเป็น ..." เพื่อสลับเองได้ --}}
                <span class="qtype" id="qType" data-mode="{{ $qMode }}" hidden></span>
                <input type="search" id="searchAll" value="{{ $qInit }}" placeholder="ค้นหา เลข SO / PO / บิล" autocomplete="off">
                <button type="button" class="qdetect" id="qDetect" hidden title="กดเพื่อเปลี่ยนว่าจะค้นเป็น SO / PO / เลขบิล"></button>
            </div>
            <input type="hidden" name="SONum" id="searchSO" value="{{ request('SONum') }}">
            <input type="hidden" name="PONum" id="searchPO" value="{{ request('PONum') }}">
            <input type="hidden" name="BillNo" id="searchBillNo" value="{{ request('BillNo') }}">
            <label class="fld f-date field-label" for="searchDate" title="วันที่เปิดบิล">
                <svg class="i" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                <span class="lbl">วันที่เปิดบิล</span>
                <input type="date" name="bill_date" id="searchDate" value="{{ $billDate }}">
            </label>

            <button type="submit" class="search-btn btn-primary" id="btnSearch" title="ค้นหา (หรือกด Enter ในช่องค้นหา)">
                <span class="sb-ic"><svg class="i" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg></span>
                <span class="sb-spin" aria-hidden="true"></span>
                <span class="sb-txt">ค้นหา</span>
                <kbd class="sb-kbd">Enter</kbd>
            </button>
            <button type="button" class="btn-icon reset" onclick="clearAllFilters()" title="ล้างตัวกรอง"><svg class="i" viewBox="0 0 24 24"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg></button>
        </form>

        @php
            $currentFilter = request('filter_status', 'pending');
            $qsWithoutFilter = request()->except(['filter_status', 'page']);

            $urlPending = url()->current() . '?' . http_build_query(array_merge($qsWithoutFilter, ['filter_status' => 'pending']));
            $urlAll     = url()->current() . '?' . http_build_query(array_merge($qsWithoutFilter, ['filter_status' => 'all']));
        @endphp

        <div class="list-meta">
            <div class="filter-pills">
                <a href="{{ $urlPending }}" class="filter-pill {{ $currentFilter === 'pending' ? 'active' : '' }}"><span class="d"></span>บิลยังค้างอยู่</a>
                <a href="{{ $urlAll }}" class="filter-pill {{ $currentFilter === 'all' ? 'active' : '' }}">ดูทั้งหมด</a>
            </div>
        </div>
    </div>

    <div class="banner-right">
        <span class="user-badge user-info" title="ผู้ใช้งาน"><span class="av"><svg class="i" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span><span class="name">{{ $creator }}</span></span>
        <a href="http://server_update:8000/solist" class="btn-icon" title="หน้าหลัก"><svg class="i" viewBox="0 0 24 24"><path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/></svg></a>
    </div>
</div>

<input type="hidden" id="inpUser" value="{{ $creator }}">

<main>
    @if ($bills->count() > 0)
    <div class="so-grid">
        @foreach ($bills as $bill)
            @php
                $soIdSafe = 'so_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $bill->so_id);
                $dnList   = $bill->bills->isNotEmpty() ? $bill->bills : collect([(object) ['dn_no' => null, 'time' => null]]);
                $sourceLabel = fn ($type) => $type === 'internal' ? 'ภายใน' : ($type === 'external' ? 'ระบบใหม่' : '');
                $poClean = fn ($raw) => preg_replace('/^\s*PO\s*/i', '', (string) $raw);
            @endphp
            <div class="so-card collapsed" id="{{ $soIdSafe }}" data-done="{{ $bill->all_done ? 1 : 0 }}">
                <div class="so-card-header" role="button" tabindex="0" aria-expanded="false"
                     onclick="toggleSoCard('{{ $soIdSafe }}')"
                     onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();toggleSoCard('{{ $soIdSafe }}');}">
                    <span class="so-toggle" aria-hidden="true"><svg class="i" viewBox="0 0 24 24"><path d="m9 6 6 6-6 6"/></svg></span>
                    @php
                        $headerBills = $bill->bills->filter(fn ($d) => !empty($d->dn_no))
                            ->unique('dn_no')->values();
                    @endphp
                    <div class="so-head-main">
                        <div class="so-id {{ $bill->all_done ? 'is-done' : '' }}">
                            <a href="http://server_update:8000/sodetail?SONum={{ urlencode($bill->so_id) }}"
                               target="_blank" rel="noopener" onclick="event.stopPropagation()" title="เปิดรายละเอียด SO">{{ $bill->so_id }}</a>
                        </div>
                        @if($headerBills->isNotEmpty())
                            <div class="so-billno-row">
                                <span class="so-billno-count">{{ $headerBills->count() }} บิล</span>
                                @foreach($headerBills as $hb)
                                    <span class="so-billno-chip {{ ($hb->cancelled ?? false) ? 'is-cancelled' : (($hb->picked ?? false) ? 'is-picked' : '') }}"
                                          title="{{ ($hb->cancelled ?? false) ? 'ยกเลิกแล้ว' : (($hb->picked ?? false) ? 'จัดบิลแล้ว' : 'รอจัดบิล') }}">{{ $hb->dn_no }}</span>
                                @endforeach
                            </div>
                        @endif
                        <div class="so-sub">
                            @if($bill->customer_id) <b>{{ $bill->customer_id }}</b> @endif
                            @if($bill->customer_id && $bill->customer_name) · @endif
                            @if($bill->customer_name) {{ $bill->customer_name }} @endif
                        </div>
                    </div>
                    @if ($bill->all_done)
                        <span class="so-status done">จัดของแล้ว</span>
                    @else
                        <span class="so-status pending">รอดำเนินการ</span>
                    @endif
                </div>

                <div class="so-body">
                    @if ($bill->bills->isEmpty())
                        <div class="no-dn-note">ยังไม่พบข้อมูลบิลขนส่ง (tblbill) ของ SO นี้</div>
                    @endif

                    @php
                        $itemHostDnIdx = collect($dnList)->search(fn ($d) => !($d->cancelled ?? false) && !($d->picked ?? false));
                        if ($itemHostDnIdx === false) {
                            $itemHostDnIdx = collect($dnList)->search(fn ($d) => !($d->cancelled ?? false));
                        }
                        if ($itemHostDnIdx === false) $itemHostDnIdx = 0;

                        $itemsElId   = $soIdSafe . '_items';
                        $selectAllId = $soIdSafe . '_selectall';
                        $hostDn      = $dnList[$itemHostDnIdx] ?? null;
                    @endphp

                    @foreach ($dnList as $dnIdx => $dn)
                        @php
                            $dnElId       = $soIdSafe . '_dn' . $dnIdx;
                            $isCancelled  = $dn->cancelled ?? false;
                            $isPicked     = $dn->picked ?? false;
                            $isItemHost   = ($dnIdx === $itemHostDnIdx);
                            // จัดบิลออก (dispatch bill) แยกอิสระจากเช็คเอ้าของ (ของติ๊กราย PO เอง)
                            $hasBill      = !$isCancelled && !$isPicked && ($dn->dn_no ?? null);
                        @endphp
                        <div class="dn-section {{ $isCancelled ? 'dn-cancelled' : '' }}" id="{{ $dnElId }}" data-dnno="{{ $dn->dn_no ?? '' }}">
                            <div class="dn-section-header">
                                @if ($hasBill)
                                    <label class="dn-check-label" title="จัดบิลออก (ไม่เกี่ยวกับเช็คเอ้าของ)">
                                        <input type="checkbox" class="chkBill" data-dnno="{{ $dn->dn_no }}" onchange="updateFloatBar()">
                                        <span>จัดบิลออก</span>
                                    </label>
                                @endif
                                <span class="dn-no {{ $isCancelled ? 'is-cancelled' : ($isPicked ? 'is-done' : '') }}">{{ ($dn->dn_no ?? null) ? $dn->dn_no : '— (ไม่มีเลขที่บิล)' }}</span>
                                @if (!empty($dn->dn_no))
                                    <label class="bill-received-label {{ ($dn->bill_received ?? false) ? 'is-received' : '' }}"
                                           title="ติ๊กว่าสโตร์ได้รับบิลนี้แล้ว (กันบิลปริ้นแล้วหาย)">
                                        <input type="checkbox" class="chkBillReceived" data-billid="{{ $dn->dn_no }}"
                                               {{ ($dn->bill_received ?? false) ? 'checked' : '' }}
                                               onchange="toggleBillReceived(this)">
                                        <span>ได้รับบิลแล้ว</span>
                                    </label>
                                    @if (($dn->bill_received ?? false) && (!empty($dn->bill_received_by) || !empty($dn->bill_received_at)))
                                        @php $brTime = !empty($dn->bill_received_at) ? ' · ' . \Carbon\Carbon::parse($dn->bill_received_at)->addYears(543)->format('d/m/Y H:i') : ''; @endphp
                                        <span class="dn-time">รับบิลโดย {{ $dn->bill_received_by ?: '-' }}{{ $brTime }}</span>
                                    @endif
                                @endif

                                @if (!empty($dn->time))
                                    <span class="dn-time">{{ \Carbon\Carbon::parse($dn->time)->addYears(543)->format('d/m/Y H:i') }}</span>
                                @endif

                                @if (!empty($dn->opened_by))
                                    <span class="dn-time">ผู้เปิดบิล: {{ $dn->opened_by }}</span>
                                @endif
                                @if ($isCancelled)
                                    <span class="dn-cancelled-badge">ยกเลิกแล้ว</span>
                                @elseif ($isPicked)
                                    @php
                                        $pBy = $dn->picked_by ?: 'ระบบเก่า';
                                        $pLegacy = (($dn->picked_legacy ?? false) && !empty($dn->picked_by)) ? ' (ระบบเก่า)' : '';
                                        $pAt = !empty($dn->picked_at) ? ' · ' . \Carbon\Carbon::parse($dn->picked_at)->addYears(543)->format('d/m/Y H:i') : '';
                                    @endphp
                                    <span class="dn-picked-badge">จัดของแล้ว โดย {{ $pBy }}{{ $pLegacy }}{{ $pAt }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <div class="dn-section" id="{{ $itemsElId }}" data-dnno="{{ $hostDn->dn_no ?? '' }}" data-selectall="{{ $selectAllId }}">
                        <div class="dn-body">
                            @forelse ($bill->groups->sortByDesc('todo') as $g)
                                <div class="po-row {{ $g->todo ? '' : 'po-row-done' }}">
                                    <div class="po-row-head">
                                        @if ($g->todo)
                                            <input type="checkbox" class="chkGroup" value="{{ $g->type }}:{{ $g->id }}"
                                                   onchange="updateDnButton(document.getElementById('{{ $itemsElId }}'))">
                                        @endif
                                        @if ($sourceLabel($g->type))
                                            <span class="source-tag {{ $g->type === 'internal' ? 't-int' : 't-ext' }}">{{ $sourceLabel($g->type) }}</span>
                                        @endif
                                        <span class="po-num">PO: {{ $poClean($g->po_display) }}</span>
                                        @if ($g->type === 'external' && ($g->multi_round ?? false))
                                            <span class="source-tag t-round">รอบที่ {{ $g->round_no }}</span>
                                        @endif
                                        @if (!$g->todo)
                                            <span class="po-done-tag"><svg class="i" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>เช็คของออกแล้ว</span>
                                        @endif
                                    </div>

                                    <div class="item-col-head"><span>ชื่อสินค้า</span><span>จำนวน</span></div>

                                    @foreach ($g->items as $it)
                                        <div class="item-row">
                                            <div class="item-name">{{ $it->item_name }}</div>
                                            <div class="item-qty">{{ rtrim(rtrim(number_format($it->item_quantity, 2), '0'), '.') }}</div>
                                        </div>
                                        @if ($g->type === 'external' && $g->todo && ($it->id ?? null))
                                            {{-- ย้ายชั้น "รายสินค้า" (บางรายการในรอบเดียวไปคนละชั้นได้) --}}
                                            <div class="item-move-row">
                                                <span class="item-move-shelf"><svg class="i" viewBox="0 0 24 24"><path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg>ชั้น: {{ $it->shelf ?: '—' }}</span>
                                                <button type="button" class="btn-move-line"
                                                        data-lineid="{{ $it->id }}"
                                                        data-name="{{ $it->item_name }}"
                                                        data-shelf="{{ $it->shelf }}"
                                                        onclick="openMoveShelfLineBtn(this)">ย้ายชั้นรายการนี้</button>
                                            </div>
                                        @endif
                                    @endforeach

                                    @if ($g->type === 'external')
                                        @php
                                            $locLines = collect($g->items)
                                                ->filter(fn ($it) => ($it->shelf ?? null) || ($it->done_by ?? null))
                                                ->unique(fn ($it) => ($it->shelf ?? '') . '|' . ($it->done_by ?? '') . '|' . ($it->done_at ?? ''))
                                                ->values();
                                        @endphp
                                        @foreach ($locLines as $it)
                                            <div class="item-row-meta">
                                                <svg class="i" viewBox="0 0 24 24"><path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg>
                                                ที่เก็บ: <b>{{ $it->shelf ?? '—' }}</b> · รับเข้าโดย: {{ $it->done_by ?? '—' }}
                                                @if (!empty($it->done_at)) ({{ \Carbon\Carbon::parse($it->done_at)->addYears(543)->format('d/m/Y H:i') }}) @endif
                                            </div>
                                        @endforeach
                                    @endif

                                    @if ($g->type !== 'external')
                                        <div class="po-row-meta">
                                            <svg class="i" viewBox="0 0 24 24"><path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/></svg>
                                            ที่เก็บ: <b>{{ $g->location ?: '—' }}</b> · จัดโดย: {{ $g->done_by ?: '—' }}
                                            @if ($g->done_at) ({{ \Carbon\Carbon::parse($g->done_at)->addYears(543)->format('d/m/Y H:i') }}) @endif
                                        </div>
                                    @endif
                                    @if (!$g->todo)
                                        <div class="po-row-meta checkout-meta">
                                            <svg class="i" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
                                            เช็คของออก{{ ($g->checkout_by ?? null) ? ' โดย ' . $g->checkout_by : '' }}
                                            @if ($g->checkout_at ?? null)
                                                ({{ \Carbon\Carbon::parse($g->checkout_at)->addYears(543)->format('d/m/Y H:i') }})
                                            @endif
                                        </div>
                                    @else
                                        <div class="po-row-meta meta-actions">
                                            <button type="button" class="btn-move-shelf"
                                                onclick="openMoveShelf('{{ $poClean($g->po_display) }}','{{ $bill->so_id }}','{{ $g->receive_id ?? '' }}')"><svg class="i" viewBox="0 0 24 24"><path d="m16 3 4 4-4 4"/><path d="M20 7H4"/><path d="m8 21-4-4 4-4"/><path d="M4 17h16"/></svg>ย้ายชั้นวาง{{ ($g->type === 'external' && ($g->multi_round ?? false)) ? ' (รอบที่ ' . $g->round_no . ')' : ' (PO/SO นี้)' }}</button>
                                        </div>
                                    @endif
                                </div>
                            @empty
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @else
        <div class="empty-state">
            <svg class="i" viewBox="0 0 24 24"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.62l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
            ไม่พบบิลที่ตรงกับเงื่อนไขการค้นหา
        </div>
    @endif

    @if ($bills->lastPage() > 1)
        <div class="pager">
            @php
                $qs = collect(request()->except('page'))->toArray();
                $mkUrl = fn($p) => url()->current() . '?' . http_build_query(array_merge($qs, ['page' => $p]));
            @endphp
            @if ($bills->currentPage() > 1)
                <a href="{{ $mkUrl(1) }}">« แรก</a>
                <a href="{{ $mkUrl($bills->currentPage() - 1) }}">‹ ก่อนหน้า</a>
            @else
                <span class="disabled">« แรก</span>
                <span class="disabled">‹ ก่อนหน้า</span>
            @endif

            @php
                $start = max(1, $bills->currentPage() - 3);
                $end   = min($bills->lastPage(), $bills->currentPage() + 3);
            @endphp
            @for ($p = $start; $p <= $end; $p++)
                @if ($p == $bills->currentPage())
                    <span class="current">{{ $p }}</span>
                @else
                    <a href="{{ $mkUrl($p) }}">{{ $p }}</a>
                @endif
            @endfor

            @if ($bills->currentPage() < $bills->lastPage())
                <a href="{{ $mkUrl($bills->currentPage() + 1) }}">ถัดไป ›</a>
                <a href="{{ $mkUrl($bills->lastPage()) }}">สุดท้าย »</a>
            @else
                <span class="disabled">ถัดไป ›</span>
                <span class="disabled">สุดท้าย »</span>
            @endif
        </div>
    @endif
</main>

<div class="checkout-floatbar" id="floatBar" hidden>
    <span class="floatbar-count">เลือกไว้ <strong id="floatCount">0</strong> รายการ</span>
    <button type="button" class="btn-ghost" onclick="clearAllChecks()">ล้างค่า</button>
    <button type="button" class="btn-success" id="floatSubmitBtn" onclick="submitAllCheckout()"><svg class="i" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>บันทึก</button>
</div>

{{-- Modal เลือกประเภทการขนส่ง (ตอนจัดบิล) --}}
<div id="transportModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-icon"><svg class="i" viewBox="0 0 24 24"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.62l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg></div>
            <div>
                <div class="modal-title">เลือกประเภทการขนส่ง</div>
                <div class="modal-sub" id="tpSub">สำหรับบิลที่กำลังจัด</div>
            </div>
        </div>
        <div class="tp-list">
            <button type="button" class="tp-opt t-company" onclick="pickTransport('company')">
                <span class="tp-ic"><svg class="i" viewBox="0 0 24 24"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.62l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg></span>
                <span><b>ขนส่งโดยรถบริษัท</b><small>รถของบริษัทไปส่งเอง</small></span>
                <svg class="i go" viewBox="0 0 24 24"><path d="m9 6 6 6-6 6"/></svg>
            </button>
            <button type="button" class="tp-opt t-private" onclick="pickTransport('private')">
                <span class="tp-ic"><svg class="i" viewBox="0 0 24 24"><path d="M21 8 12 3 3 8v8l9 5 9-5z"/><path d="m3 8 9 5 9-5M12 13v8"/></svg></span>
                <span><b>บริษัทขนส่ง (เอกชน)</b><small>ส่งผ่านบริษัทขนส่งภายนอก</small></span>
                <svg class="i go" viewBox="0 0 24 24"><path d="m9 6 6 6-6 6"/></svg>
            </button>
            <button type="button" class="btn-ghost tp-cancel" onclick="closeTransport()">ยกเลิก</button>
        </div>
    </div>
</div>

{{-- Modal ย้ายชั้นวาง (ต่อ PO+SO) --}}
<div id="moveShelfModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-icon"><svg class="i" viewBox="0 0 24 24"><path d="m16 3 4 4-4 4"/><path d="M20 7H4"/><path d="m8 21-4-4 4-4"/><path d="M4 17h16"/></svg></div>
            <div>
                <div class="modal-title">ย้ายชั้นวาง</div>
                <div class="modal-sub" id="msLabel"></div>
            </div>
        </div>
        <div class="ms-field">
            <svg class="i" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
            <input type="search" id="msShelf" placeholder="เลือกหรือพิมพ์ชั้นวาง..." autocomplete="off"
                   oninput="renderShelfOptions(this.value)" onfocus="renderShelfOptions(this.value)">
            <div id="msShelfList" class="ms-shelf-list"></div>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn-ghost" onclick="closeMoveShelf()">ยกเลิก</button>
            <button type="button" class="btn-success" id="msSaveBtn" onclick="confirmMoveShelf()">ย้าย</button>
        </div>
    </div>
</div>

<!-- กล่องยืนยัน / แจ้งเตือน -->
<div id="uiDialog" class="ui-dlg-overlay">
    <div class="ui-dlg" role="alertdialog" aria-modal="true">
        <div class="ui-dlg-icon"></div>
        <div class="ui-dlg-title" id="uiDlgTitle"></div>
        <div class="ui-dlg-msg" id="uiDlgMsg"></div>
        <div class="ui-dlg-actions">
            <button type="button" class="btn-ghost" id="uiDlgCancel">ยกเลิก</button>
            <button type="button" class="btn-primary" id="uiDlgOk">ยืนยัน</button>
        </div>
    </div>
</div>
<div id="uiToast" class="ui-toast" role="status" aria-live="polite"></div>

<script>
const SUBMIT_URL = "{{ route('store.checkout.submit') }}";
const MOVE_SHELF_URL = "{{ route('shelfsale.move') }}";
const BILL_RECEIVED_URL = "{{ route('store.checkout.billReceived') }}";
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

/* ===== กล่องยืนยัน + แจ้งเตือน (แทน confirm / alert ของเบราว์เซอร์) ===== */
const IC_CHECK = '<svg class="i" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>';
const IC_WARN  = '<svg class="i" viewBox="0 0 24 24"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/></svg>';
function uiConfirm(o){
    return new Promise(resolve => {
        const ov = document.getElementById('uiDialog'), box = ov.querySelector('.ui-dlg');
        const ok = document.getElementById('uiDlgOk'), cancel = document.getElementById('uiDlgCancel');
        box.className = 'ui-dlg t-' + (o.tone || 'info');
        box.querySelector('.ui-dlg-icon').innerHTML = o.icon || (o.tone === 'warn' ? IC_WARN : IC_CHECK);
        document.getElementById('uiDlgTitle').textContent = o.title || '';
        document.getElementById('uiDlgMsg').textContent = o.message || '';
        ok.textContent = o.okText || 'ยืนยัน';
        let settled = false;
        const done = v => { if (settled) return; settled = true; ov.classList.remove('open'); ok.onclick = cancel.onclick = ov.onclick = null; document.removeEventListener('keydown', onKey); resolve(v); };
        const onKey = e => { if (e.key === 'Escape') done(false); };
        ok.onclick = () => done(true); cancel.onclick = () => done(false);
        ov.onclick = e => { if (e.target === ov) done(false); };
        setTimeout(() => document.addEventListener('keydown', onKey), 0);
        ov.classList.add('open'); setTimeout(() => ok.focus(), 30);     // Enter = ยืนยัน
    });
}
let toastTimer = null;
function uiToast(text, tone){
    const t = document.getElementById('uiToast');
    t.className = 'ui-toast' + (tone ? ' t-' + tone : '');
    t.textContent = text;
    requestAnimationFrame(() => t.classList.add('show'));
    clearTimeout(toastTimer); toastTimer = setTimeout(() => t.classList.remove('show'), 3200);
}

/* ===== Dropdown เลือกชั้นวาง (custom) ===== */
const SHELF_OPTIONS = @json(\App\Http\Controllers\ShelfsaleController::SHELF_OPTIONS);
function _msEscHtml(s){ return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }
function renderShelfOptions(q){
    const list = document.getElementById('msShelfList');
    if(!list) return;
    const query = (q||'').trim().toLowerCase();
    const items = SHELF_OPTIONS.filter(s => !query || String(s).toLowerCase().includes(query));
    list.innerHTML = items.map(s => `<div class="ms-shelf-opt" data-val="${_msEscHtml(s)}">${_msEscHtml(s)}</div>`).join('');
    list.style.display = items.length ? 'block' : 'none';
}
document.addEventListener('DOMContentLoaded', function(){
    const list = document.getElementById('msShelfList');
    const inp  = document.getElementById('msShelf');
    if(list){
        list.addEventListener('mousedown', function(e){
            const opt = e.target.closest('.ms-shelf-opt');
            if(!opt) return;
            e.preventDefault();
            if(inp) inp.value = opt.dataset.val;
            list.style.display = 'none';
        });
    }
    if(inp){
        inp.addEventListener('blur', () => setTimeout(() => { if(list) list.style.display = 'none'; }, 150));
        inp.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); list.style.display = 'none'; confirmMoveShelf(); } });
    }
    // กดที่หัว PO = ติ๊ก/เอาติ๊กออก (ยกเว้นกดปุ่ม/ลิงก์/ช่องติ๊กเอง)
    document.querySelectorAll('.po-row-head').forEach(h => h.addEventListener('click', e => {
        if (e.target.closest('a,button,input,label')) return;
        const cb = h.querySelector('.chkGroup'); if (!cb) return;
        cb.checked = !cb.checked;
        cb.dispatchEvent(new Event('change', { bubbles: true }));
    }));
    // ปุ่มค้นหา: แสดงสถานะกำลังค้นหา
    const form = document.getElementById('filterForm');
    if (form) form.addEventListener('submit', () => { const b = document.getElementById('btnSearch'); if (b) b.classList.add('loading'); });
    // กด Esc ปิดป๊อปอัพ / กดพื้นหลังเพื่อปิด
    document.addEventListener('keydown', e => {
        if (e.key !== 'Escape' || document.getElementById('uiDialog').classList.contains('open')) return;
        if (document.getElementById('moveShelfModal').style.display === 'flex') closeMoveShelf();
        else if (document.getElementById('transportModal').style.display === 'flex') closeTransport();
    });
    document.getElementById('moveShelfModal').addEventListener('click', e => { if (e.target.id === 'moveShelfModal') closeMoveShelf(); });
    document.getElementById('transportModal').addEventListener('click', e => { if (e.target.id === 'transportModal') closeTransport(); });
});

/* ===== สโตร์ติ๊กว่าได้รับบิลแล้ว (บันทึก tblbill.status_bill) ===== */
async function toggleBillReceived(cb){
    const billid = cb.dataset.billid;
    const label  = cb.closest('.bill-received-label');
    // เอาติ๊กออก -> ยืนยันก่อน
    if(!cb.checked){
        const ok = await uiConfirm({ tone:'warn', title:'เอาการติ๊ก "ได้รับบิลแล้ว" ออก?', message:'บิล ' + billid, okText:'เอาออก' });
        if(!ok){
            cb.checked = true;   // ยกเลิก -> ติ๊กกลับ
            return;
        }
    }
    const received = cb.checked;
    cb.disabled = true;
    try{
        const res = await fetch(BILL_RECEIVED_URL, {
            method:'POST',
            headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json','X-Requested-With':'XMLHttpRequest' },
            body: JSON.stringify({ billid: billid, received: received }),
        });
        const data = await res.json();
        if(res.ok && data.ok){
            if(label) label.classList.toggle('is-received', received);
            uiToast(received ? 'บันทึกว่าได้รับบิล ' + billid + ' แล้ว' : 'เอาการติ๊กบิล ' + billid + ' ออกแล้ว', 'success');
        } else {
            cb.checked = !received;   // rollback
            uiToast(data.message || 'บันทึกไม่สำเร็จ', 'error');
        }
    }catch(e){
        cb.checked = !received;       // rollback
        uiToast('เชื่อมต่อไม่สำเร็จ', 'error');
    }finally{ cb.disabled = false; }
}

/* ===== ย้ายชั้นวาง (ต่อ PO+SO) ===== */
let msTarget = null;
function openMoveShelf(po, so, receiveId){
    msTarget = { mode:'round', po: po, so: so, receive_id: (receiveId || '') };
    document.getElementById('msLabel').textContent = 'PO ' + po + ' / SO ' + so + (receiveId ? ' · รอบรับเข้า #' + receiveId : '');
    document.getElementById('msShelf').value = '';
    document.getElementById('moveShelfModal').style.display = 'flex';
    setTimeout(() => document.getElementById('msShelf').focus(), 30);
}
// ย้ายชั้น "รายสินค้า" (line เดียว) — บางรายการในรอบเดียวไปคนละชั้น
function openMoveShelfLineBtn(btn){
    const lineId = btn.dataset.lineid;
    const name   = btn.dataset.name || '';
    const shelf  = btn.dataset.shelf || '';
    msTarget = { mode:'line', line_id: lineId, name: name };
    document.getElementById('msLabel').textContent = 'สินค้า: ' + name + (shelf ? ' · ชั้นเดิม ' + shelf : '');
    document.getElementById('msShelf').value = '';
    document.getElementById('moveShelfModal').style.display = 'flex';
    setTimeout(() => document.getElementById('msShelf').focus(), 30);
}
function closeMoveShelf(){ document.getElementById('moveShelfModal').style.display = 'none'; msTarget = null; }
async function confirmMoveShelf(){
    if (!msTarget) return;
    const shelf = document.getElementById('msShelf').value.trim();
    if (!shelf){ uiToast('กรุณาเลือกชั้นวาง', 'error'); document.getElementById('msShelf').focus(); return; }
    const btn = document.getElementById('msSaveBtn'); btn.disabled = true; btn.textContent = 'กำลังย้าย...';
    try {
        const res = await fetch(MOVE_SHELF_URL, {
            method: 'POST',
            headers: { 'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json' },
            body: JSON.stringify(
                msTarget.mode === 'line'
                    ? { line_id: msTarget.line_id, shelf: shelf }
                    : { po: msTarget.po, so: msTarget.so, shelf: shelf, po_receive_id: (msTarget.receive_id || null) }
            )
        });
        const data = await res.json().catch(() => null);
        if (!res.ok || !data || !data.ok){ uiToast((data && data.message) || 'ย้ายชั้นไม่สำเร็จ', 'error'); btn.disabled = false; btn.textContent = 'ย้าย'; return; }
        uiToast('ย้ายไปชั้น ' + shelf + ' แล้ว', 'success');
        setTimeout(() => window.location.reload(), 500);
    } catch(e){ console.error(e); uiToast('เกิดข้อผิดพลาด', 'error'); btn.disabled = false; btn.textContent = 'ย้าย'; }
}

/* ===== ช่องค้นหาเดียว: แยกให้เองว่าเป็น SO / PO / เลขบิล แล้วใส่ช่องเดิม (name SONum / PONum / BillNo) ===== */
const Q_LABEL = { auto:'อัตโนมัติ', so:'เลข SO', po:'เลข PO', bill:'เลขบิล' };
function detectQType(q){
    q = (q || '').trim();
    if (!q) return '';
    if (/^po/i.test(q) || q.includes('-') || /^A\d/i.test(q)) return 'po';     // PO เช่น PO6909-02397, 6910-A0092
    if (/^so/i.test(q)) return 'so';
    if (/^[a-z]/i.test(q)) return 'bill';                                      // ขึ้นต้นด้วยตัวอักษรอื่น = เลขบิล
    return 'so';                                                               // ตัวเลข / มี "/" = SO
}
function currentQType(){
    const mode = document.getElementById('qType').dataset.mode;
    return mode === 'auto' ? detectQType(document.getElementById('searchAll').value) : mode;
}
function syncQType(){
    const mode = document.getElementById('qType').dataset.mode;
    const d = document.getElementById('qDetect'), t = currentQType();
    d.hidden = !t;
    d.textContent = t ? 'ค้นเป็น ' + Q_LABEL[t] : '';
    d.classList.toggle('manual', mode !== 'auto');
}
function fillHiddenSearch(){
    const q = document.getElementById('searchAll').value.trim(), t = currentQType();
    document.getElementById('searchSO').value     = t === 'so'   ? q : '';
    document.getElementById('searchPO').value     = t === 'po'   ? q : '';
    document.getElementById('searchBillNo').value = t === 'bill' ? q : '';
}
document.addEventListener('DOMContentLoaded', function(){
    const box = document.getElementById('qType'), all = document.getElementById('searchAll');
    // กดป้าย "ค้นเป็น ..." = สลับประเภทเอง SO -> PO -> เลขบิล -> กลับเป็นอัตโนมัติ
    document.getElementById('qDetect').addEventListener('click', () => {
        const order = ['so', 'po', 'bill'];
        const shown = currentQType() || 'so';
        box.dataset.mode = (box.dataset.mode !== 'auto' && shown === 'bill') ? 'auto' : order[(order.indexOf(shown) + 1) % 3];
        syncQType(); all.focus();
    });
    all.addEventListener('input', () => { if (!all.value.trim()) box.dataset.mode = 'auto'; syncQType(); });
    // ส่งฟอร์ม (กดค้นหา / Enter) -> ใส่ค่าให้ช่องเดิมที่ตรงประเภทก่อน
    document.getElementById('filterForm').addEventListener('submit', fillHiddenSearch, true);
    syncQType();
});

let searchTimer;
function triggerAutoSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        if (document.getElementById('searchAll').value.trim()) fillHiddenSearch();
        const soVal = document.getElementById('searchSO').value.trim();
        const poVal = document.getElementById('searchPO').value.trim();
        const billVal = document.getElementById('searchBillNo').value.trim();

        if (soVal === '' && poVal === '' && billVal === '') {
            const currentFilter = "{{ request('filter_status', 'pending') }}";
            const url = new URL(window.location.href);
            url.searchParams.delete('SONum');
            url.searchParams.delete('PONum');
            url.searchParams.delete('BillNo');
            url.searchParams.set('filter_status', currentFilter);
            window.location.href = url.toString();
            return;
        }
        document.getElementById('filterForm').submit();
    }, 500);
}

function triggerDateSearch() {
    document.getElementById('filterForm').submit();
}

function clearAllFilters() {
    const currentFilter = "{{ request('filter_status', 'pending') }}";
    const url = new URL(window.location.href);
    url.search = '';
    url.searchParams.set('filter_status', currentFilter);
    window.location.href = url.toString();
}

// เปลี่ยนเป็น "กดค้นหา" เท่านั้น — ไม่ค้นหาอัตโนมัติระหว่างพิมพ์/เปลี่ยนวันแล้ว
// (ฟอร์มมีปุ่ม submit "ค้นหา" แล้ว กด Enter ในช่องค้นหาก็ submit ได้ตามปกติ)

function toggleSoCard(id) {
    const card = document.getElementById(id);
    if (!card) return;
    const willOpen = card.classList.contains('collapsed');   // ตอนนี้ปิดอยู่ = กำลังจะเปิด
    if (willOpen) {
        // accordion: เปิดอันใหม่ -> ปิดอันที่เปิดค้างอยู่ทั้งหมด
        document.querySelectorAll('.so-card:not(.collapsed)').forEach(other => {
            if (other === card) return;
            other.classList.add('collapsed');
            const h = other.querySelector('.so-card-header');
            if (h) h.setAttribute('aria-expanded', 'false');
        });
    }
    const collapsed = card.classList.toggle('collapsed');
    const header = card.querySelector('.so-card-header');
    if (header) header.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
}

function updateDnButton(itemsEl) {
    // sync เฉพาะ "เลือกของทั้งหมด" กับ checkbox ของ (chkGroup) เท่านั้น — ไม่ไปยุ่งกับ checkbox บิล
    const allBoxes = itemsEl.querySelectorAll('.chkGroup');
    const checked  = itemsEl.querySelectorAll('.chkGroup:checked').length;
    const selectAll = document.getElementById(itemsEl.dataset.selectall);
    if (selectAll) {
        selectAll.checked       = allBoxes.length > 0 && checked === allBoxes.length;
        selectAll.indeterminate = checked > 0 && checked < allBoxes.length;
    }
    updateFloatBar();
}

function toggleDnSelectAll(dnEl, checked) {
    dnEl.querySelectorAll('.chkGroup').forEach(cb => { cb.checked = checked; });
    updateDnButton(dnEl);
}

function updateFloatBar() {
    const checkedGroups = document.querySelectorAll('.chkGroup:checked').length;  // เช็คเอ้าของ
    const checkedBills  = document.querySelectorAll('.chkBill:checked').length;   // จัดบิลออก
    const total = checkedGroups + checkedBills;
    const bar = document.getElementById('floatBar');
    const cnt = document.getElementById('floatCount');
    if (cnt) cnt.textContent = total;
    if (bar) bar.hidden = total === 0;
    document.body.classList.toggle('has-floatbar', total > 0);
}

function clearAllChecks() {
    document.querySelectorAll('.chkGroup:checked').forEach(cb => { cb.checked = false; });
    document.querySelectorAll('.chkBill:checked').forEach(cb => { cb.checked = false; });
    document.querySelectorAll('.dnSelectAll').forEach(cb => { cb.checked = false; cb.indeterminate = false; });
    updateFloatBar();
}

let pendingSubmit = null;   // เก็บ {ids, dnNos, totalCount} ระหว่างรอเลือกประเภทขนส่ง

async function submitAllCheckout() {
    // เช็คเอ้าของ (ids) มาจาก checkbox ของ (chkGroup) เท่านั้น
    // จัดบิลออก (dnNos) มาจาก checkbox บิล (chkBill) ที่ติ๊กเองเท่านั้น — ไม่ derive จาก PO
    const checkedBoxes = Array.from(document.querySelectorAll('.chkGroup:checked'));
    const billBoxes    = Array.from(document.querySelectorAll('.chkBill:checked'));
    if (!checkedBoxes.length && !billBoxes.length) return;

    const ids   = Array.from(new Set(checkedBoxes.map(c => c.value)));
    const dnNos = Array.from(new Set(billBoxes.map(c => c.dataset.dnno).filter(Boolean)));

    const totalCount = ids.length + dnNos.length;
    pendingSubmit = { ids, dnNos, totalCount };

    // มีจัดบิลออก -> ต้องเลือกประเภทขนส่งก่อน
    if (dnNos.length > 0) {
        document.getElementById('tpSub').textContent = 'จัดบิลออก ' + dnNos.length + ' บิล' + (ids.length ? ' + เช็คของออก ' + ids.length + ' PO' : '') + ' · ' + dnNos.join(', ');
        document.getElementById('transportModal').style.display = 'flex';
        return;
    }
    // เช็คเอ้าของอย่างเดียว (ไม่จัดบิล) -> ยืนยันแล้วส่งเลย
    const ok = await uiConfirm({ tone:'success', title:'ยืนยันบันทึกข้อมูล', message:'เช็คของออก ' + totalCount + ' รายการใช่หรือไม่?', okText:'บันทึก' });
    if (!ok) { pendingSubmit = null; return; }
    doSubmit(null);
}

function closeTransport() {
    document.getElementById('transportModal').style.display = 'none';
    pendingSubmit = null;
}

function pickTransport(type) {
    document.getElementById('transportModal').style.display = 'none';
    if (!pendingSubmit) return;
    doSubmit(type);
}

async function doSubmit(transportType) {
    if (!pendingSubmit) return;
    const { ids, dnNos } = pendingSubmit;
    const btn = document.getElementById('floatSubmitBtn');
    btn.disabled = true;
    try {
        const res = await fetch(SUBMIT_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify({ ids, dn_nos: dnNos, transport_type: transportType }),
        });
        const data = await res.json();
        if (res.ok && data.ok) {
            uiToast(data.message || 'บันทึกเรียบร้อย', 'success');
            setTimeout(() => window.location.reload(), 600);
        } else {
            uiToast(data.message || 'บันทึกไม่สำเร็จ', 'error');
            btn.disabled = false;
        }
    } catch (e) {
        console.error(e);
        uiToast('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
        btn.disabled = false;
    } finally {
        pendingSubmit = null;
    }
}
</script>
</body>
</html>