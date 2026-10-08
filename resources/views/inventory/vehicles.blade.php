<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>ค้นหาสินค้า - 3E TRADING</title>
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Sarabun',Arial,sans-serif;background:#f9fafb;min-height:100vh;padding-bottom:40px;color:#1f2937}
    
    /* Overlay & Block Loading */
    .ov{position:fixed;inset:0;background:rgba(0,0,0,.85);display:flex;justify-content:center;align-items:center;z-index:9999;opacity:0;visibility:hidden;transition:opacity .3s,visibility .3s;backdrop-filter:blur(4px)}
    .ov.on{opacity:1;visibility:visible}
    .progress-container{width: 320px; text-align: center;}
    .ov-text{color:#fff;font-size:18px;font-weight:600;margin-bottom:20px;letter-spacing: 0.5px;}
    .progress-track{
      width: 100%; 
      height: 32px; 
      background: rgba(255,255,255,0.1); 
      border: 2px solid rgba(255,255,255,0.3);
      border-radius: 4px; 
      overflow: hidden; 
      margin-bottom: 12px;
      box-shadow: inset 0 2px 4px rgba(0,0,0,0.3);
    }
    .progress-fill{
      width: 0%; 
      height: 100%; 
      background-color: #5B65F3;
      background-image: repeating-linear-gradient(
        90deg,
        #5B65F3 0px,
        #5B65F3 18px,
        rgba(255,255,255,0.15) 18px,
        rgba(255,255,255,0.15) 20px
      );
      background-size: 20px 100%;
      transition: width 0.15s ease-out;
      box-shadow: 0 0 15px rgba(91,101,243,0.6);
    }
    .ov-percent{color:#fff;font-size:16px;font-weight:700;font-variant-numeric: tabular-nums; text-shadow: 0 2px 4px rgba(0,0,0,0.3);}

    .topbar{height:64px;background:#fff;display:flex;align-items:center;gap:12px;padding:0 24px;position:fixed;top:0;left:0;right:0;z-index:2000;border-bottom:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,.05)}
    .topbar-logo{height:36px;border-radius:6px}
    .topbar-title{font-size:18px;font-weight:700;color:#111827;flex:1;letter-spacing:-0.025em}
    .topbar-right{display:flex;align-items:center;gap:12px}
    .topbar-name{font-size:14px;color:#6b7280;font-weight:500}
    .topbar-badge{font-size:12px;padding:4px 10px;font-weight:600;color:#5B65F3;background:#EEF2FF;border-radius:6px;border:1px solid #C7D2FE}
    .hamburger{background:none;border:none;cursor:pointer;padding:8px;border-radius:8px;display:flex;flex-direction:column;gap:5px;flex-shrink:0;transition:background .2s}
    .hamburger span{display:block;width:20px;height:2px;background:#374151;transition:background .2s}
    .hamburger:hover{background:#f3f4f6}
    .hamburger:hover span{background:#5B65F3}
    .sb-ov{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1500;opacity:0;pointer-events:none;transition:opacity .2s}
    .sb-ov.open{opacity:1;pointer-events:all}
    .sidebar{position:fixed;top:0;left:-280px;width:260px;height:100vh;z-index:1600;transition:left .3s ease;display:flex;flex-direction:column;background:#fff;border-right:1px solid #e5e7eb;box-shadow:4px 0 24px rgba(0,0,0,.08)}
    .sidebar.open{left:0}
    .sb-head{display:flex;align-items:center;gap:12px;padding:16px 20px;background:#fff;border-bottom:1px solid #e5e7eb;min-height:64px}
    .sb-head img{height:32px;border-radius:6px}
    .sb-head span{font-size:18px;font-weight:700;color:#111827;flex:1;letter-spacing:-0.025em}
    .sb-close{background:none;border:none;color:#6b7280;cursor:pointer;font-size:20px;font-weight:bold;padding:4px 8px;border-radius:6px;transition:all .2s}
    .sb-close:hover{background:#f3f4f6;color:#111827}
    .sb-nav{flex:1;overflow-y:auto;padding:12px 0}
    .sb-sec{padding:12px 20px 6px;font-size:11px;font-weight:700;color:#6b7280;letter-spacing:.05em;text-transform:uppercase}
    .sb-item{display:flex;align-items:center;gap:12px;padding:10px 20px;color:#374151;cursor:pointer;font-size:14px;font-weight:500;border-left:3px solid transparent;user-select:none;text-decoration:none;transition:all .2s;border-radius:0 8px 8px 0;margin-right:8px}
    .sb-item:hover{background:#f9fafb;border-left-color:#5B65F3;color:#111827}
    .sb-item.cur{background:#EEF2FF;border-left-color:#5B65F3;color:#5B65F3;font-weight:600}
    #content{padding:88px 16px 24px;width:100%}
    .card{margin-bottom:20px;padding:20px;background:#fff;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,.05)}
    .card h2{font-size:20px;font-weight:700;color:#111827;margin-bottom:16px;letter-spacing:-0.025em}
    .abar{display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end}
    .abar input,.abar select{padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;font-family:'Sarabun',sans-serif;background:#fff;transition:all .2s;color:#111827;flex:1;min-width:120px}
    .abar input:focus,.abar select:focus{outline:none;border-color:#5B65F3;box-shadow:0 0 0 3px rgba(91,101,243,.1)}
    .btn{padding:10px 20px;font-size:14px;font-weight:600;font-family:'Sarabun',sans-serif;cursor:pointer;white-space:nowrap;border:none;border-radius:8px;transition:all .2s}
    .btn-add{background:#10b981;color:#fff}
    .btn-add:hover{background:#059669}
    .btn-clr{background:#fff;color:#374151;border:1px solid #d1d5db}
    .btn-clr:hover{background:#f9fafb;border-color:#9ca3af}
    .btn-edit{background:#5B65F3;color:#fff}
    .btn-edit:hover{background:#4F46E5}
    .btn-save{background:#10b981;color:#fff}
    .btn-save:hover{background:#059669}
    .btn-del{background:#ef4444;color:#fff}
    .btn-del:hover{background:#dc2626}
    .btn-can{background:#e5e7eb;color:#374151}
    .btn-can:hover{background:#d1d5db}
    .tbl-wrap{background:#fff;overflow-x:auto;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.05)}

    /* ===================== การ์ดสินค้าแบบพลิก (Flip Card) ===================== */
    /* การ์ดขนาดคงที่ 360×500 เท่าต้นฉบับ สูงสุดแถวละ 5 ใบ (จอแคบจะลดจำนวนต่อแถวเอง) */
    .v-grid{display:grid;grid-template-columns:repeat(auto-fill,360px);gap:16px;justify-content:center;max-width:1864px;margin:0 auto}
    @media(max-width:400px){.v-grid{grid-template-columns:1fr}}

    .v-card{perspective:2000px;display:flex;outline:none;cursor:pointer}
    /* ใช้ easing แบบสมมาตร เพื่อให้ครึ่งเวลา = หมุนได้ 90° พอดี (จังหวะสลับหน้า) */
    .v-inner{flex:1;display:grid;position:relative;transform-style:preserve-3d;-webkit-transform-style:preserve-3d;transition:transform .7s cubic-bezier(.45,.05,.55,.95);will-change:transform}
    .v-card:hover .v-inner,
    .v-card.flipped .v-inner,
    .v-card:has(:focus-visible) .v-inner,
    .v-card:focus-visible .v-inner{transform:rotateY(180deg)}
    @media(hover:none){.v-card:hover .v-inner{transform:none}.v-card.flipped .v-inner{transform:rotateY(180deg)}}

    .v-face{grid-area:1/1;backface-visibility:hidden;-webkit-backface-visibility:hidden;background:#fff;border:1px solid #e5e7eb;border-radius:18px;box-shadow:0 1px 3px rgba(0,0,0,.05);display:flex;flex-direction:column;min-height:390px;transition:box-shadow .3s,visibility 0s linear .35s}
    .v-card:hover .v-face{box-shadow:0 12px 32px rgba(17,24,39,.12)}
    .v-front{transform:rotateY(0deg) translateZ(1px);visibility:visible}
    .v-back{transform:rotateY(180deg) translateZ(1px);visibility:hidden}
    /* สลับการมองเห็นตอนหมุนถึงครึ่งทาง กันด้านหน้า-หลังซ้อนทะลุกัน (แก้บั๊ก backface ของบางเบราว์เซอร์) */
    .v-card:hover .v-front,
    .v-card.flipped .v-front,
    .v-card:has(:focus-visible) .v-front,
    .v-card:focus-visible .v-front{visibility:hidden}
    .v-card:hover .v-back,
    .v-card.flipped .v-back,
    .v-card:has(:focus-visible) .v-back,
    .v-card:focus-visible .v-back{visibility:visible}
    @media(hover:none){
      .v-card:hover .v-front{visibility:visible}
      .v-card:hover .v-back{visibility:hidden}
      .v-card.flipped .v-front{visibility:hidden}
      .v-card.flipped .v-back{visibility:visible}
    }
    @media(prefers-reduced-motion:reduce){.v-inner{transition:none}.v-face{transition:none}}
    .v-card.sub .v-face{background:#fbfbfd}

    /* ---- ด้านหน้า ---- */
    .vf-media{position:relative;padding:8px 8px 0}
    .vf-img{height:180px;border-radius:12px;overflow:hidden;background:#f3f4f6}
    .vf-img img{width:100%;height:100%;object-fit:cover;display:block}
    .vf-ph{width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;color:#9ca3af;font-size:12px;font-weight:600;background:linear-gradient(135deg,#f3f4f6,#e5e7eb)}
    .vf-pill{position:absolute;left:18px;bottom:10px;display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:999px;background:rgba(255,255,255,.94);color:#111827;font-size:13px;font-weight:700;box-shadow:0 2px 8px rgba(0,0,0,.12)}
    .vf-pill svg{color:#f59e0b}
    .vf-pill.zero{color:#b91c1c}
    .vf-pill.zero svg{color:#ef4444}
    .vf-sub{position:absolute;right:18px;top:18px;padding:4px 10px;border-radius:999px;background:rgba(17,24,39,.75);color:#fff;font-size:11px;font-weight:600}
    .vf-body{flex:1;display:flex;flex-direction:column;gap:6px;padding:16px 20px 18px}
    .vf-company{display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:#111827}
    .vf-company .badge{padding:2px 8px;font-size:11px}
    .vf-company-name{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;min-width:0}
    .vf-name{font-size:18px;font-weight:700;color:#111827;line-height:1.35;letter-spacing:-0.01em;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
    .vf-id{display:flex;align-items:center;gap:6px;font-size:14px;color:#6b7280}
    .vf-hint{margin-top:auto;padding-top:12px;display:flex;align-items:center;justify-content:space-between;font-size:13px;font-weight:600;color:#6b7280;letter-spacing:.03em}
    .vf-hint .h-touch{display:none}
    @media(hover:none){.vf-hint .h-hover{display:none}.vf-hint .h-touch{display:inline}}

    /* ---- ด้านหลัง ---- */
    .v-back{align-items:center;justify-content:center;gap:14px;padding:22px 18px;text-align:center}
    .vb-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;width:100%}
    .vb-stat{background:#f6f6f7;border:1px solid #eeeeef;border-radius:14px;padding:10px 4px;display:flex;flex-direction:column;align-items:center;gap:6px;min-width:0}
    .vb-ic{width:36px;height:36px;border-radius:10px;background:#fff;display:flex;align-items:center;justify-content:center;color:#111827;box-shadow:0 1px 3px rgba(0,0,0,.08)}
    .vb-stat strong{font-size:13px;font-weight:700;color:#111827;max-width:100%;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .vb-stat small{font-size:11px;color:#9ca3af;font-weight:500;margin-top:-4px}
    .vb-title{font-size:17px;font-weight:700;color:#111827;margin-top:4px}
    .vb-note{font-size:13px;color:#6b7280;line-height:1.6;max-width:260px;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
    .vb-cta{display:inline-flex;align-items:center;justify-content:center;gap:8px;width:100%;max-width:240px;padding:12px 18px;border:none;border-radius:12px;background:#18181b;color:#fff;font-family:'Sarabun',sans-serif;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 10px 24px rgba(0,0,0,.18);transition:box-shadow .2s,background .2s}
    .vb-cta:hover{background:#000;box-shadow:0 14px 28px rgba(0,0,0,.24)}
    .vb-admin{display:flex;gap:8px}
    .vb-admin .btn{padding:6px 14px;font-size:13px;border-radius:8px}

    /* ---- มิติความลึก 3D: แต่ละชั้นลอยออกมาไม่เท่ากันตอนหมุน (แบบ Perspective Card) ---- */
    .v-face,.vf-media,.vf-body,.vf-text,.vb-stats,.vb-stat{transform-style:preserve-3d;-webkit-transform-style:preserve-3d}
    /* ด้านหน้า */
    .vf-media{transform:translateZ(50px)}
    .vf-img{border:1px solid rgba(229,231,235,.7)}
    .vf-img img{transition:transform .7s ease}
    .v-card:hover .vf-img img,.v-card.flipped .vf-img img{transform:scale(1.1)}
    .vf-pill,.vf-sub{transform:translateZ(30px)}
    .vf-text{display:flex;flex-direction:column;gap:6px;transform:translateZ(60px)}
    .vf-name{transition:color .3s}
    .v-card:hover .vf-name{color:#5B65F3}
    .vf-hint{transform:translateZ(40px);transition:color .3s}
    .vf-hint>span{transition:transform .3s}
    .v-card:hover .vf-hint{color:#5B65F3}
    .v-card:hover .vf-hint>span{transform:translateX(4px)}
    /* ด้านหลัง */
    .vb-stat:nth-child(1),.vb-stat:nth-child(3){transform:translateZ(120px)}
    .vb-stat:nth-child(2){transform:translateZ(150px)}
    .vb-ic{transform:translateZ(20px)}
    .vb-stat strong,.vb-stat small{transform:translateZ(10px)}
    .vb-title{transform:translateZ(80px)}
    .vb-note{transform:translateZ(40px)}
    .vb-admin{transform:translateZ(70px)}
    .vb-cta{transform:translateZ(100px);transition:transform .2s,box-shadow .2s,background .2s}
    .vb-cta:hover{transform:translateZ(100px) scale(1.03)}
    .vb-cta:active{transform:translateZ(100px) scale(.95)}

    /* ---- ขนาดและสัดส่วนเท่าการ์ดต้นฉบับ (360×500) ---- */
    .v-card{width:360px;height:500px}
    .v-face{height:500px;min-height:0;border-radius:16px}
    .vf-media{padding:12px 12px 0}
    .vf-img{height:256px;border-radius:12px}
    .vf-pill{left:28px;bottom:16px;padding:7px 14px;font-size:13px}
    .vf-sub{right:28px;top:28px}
    .vf-body{padding:28px 24px 24px}
    .vf-text{gap:8px}
    .vf-company{font-size:12px;letter-spacing:.02em}
    .vf-name{font-size:24px;line-height:1.25}
    .vf-id{font-size:15px;font-weight:500}
    .vf-hint{font-size:12px;letter-spacing:.05em}
    .v-back{padding:32px;gap:0}
    .vb-stats{display:flex;justify-content:center;gap:12px;width:auto}
    .vb-stat{min-width:88px;max-width:88px;padding:16px 8px;border-radius:16px;gap:8px}
    .vb-ic{width:42px;height:42px;border-radius:12px}
    .vb-ic svg{width:24px;height:24px}
    .vb-stat strong{font-size:12px}
    .vb-title{font-size:20px;margin-top:36px}
    .vb-note{font-size:13px;max-width:280px;margin-top:10px}
    .vb-cta{height:44px;max-width:none;margin-top:24px;padding:0 18px;font-size:13px;letter-spacing:.04em}
    .vb-admin{margin-top:12px}
    @media(max-width:400px){.v-card{width:100%}}

    table{width:100%;border-collapse:collapse;font-size:14px}
    thead{background:#f9fafb}
    th{color:#374151;padding:14px 16px;text-align:left;font-weight:600;font-size:13px;white-space:nowrap;position:sticky;top:0;background:#f9fafb;z-index:5;border:1px solid #e5e7eb;border-top:none}
    th:first-child{border-left:none}
    th:last-child{border-right:none}
    td{padding:12px 16px;border:1px solid #e5e7eb;color:#1f2937;font-size:14px;vertical-align:middle}
    td:first-child{border-left:none}
    td:last-child{border-right:none}
    tbody tr:hover{background:#f9fafb}
    tr.sub-row{background:#f9fafb}
    tr.sub-row td{padding:10px 16px;font-size:13px;border:1px solid #e5e7eb;border-top:none}
    tr.sub-row:hover{background:#f3f4f6}
    tr.sub-row.hide{display:none}
    tr.sub-form-row td{background:#EEF2FF;border:1px solid #e5e7eb;border-top:none;padding:16px;overflow:visible;white-space:normal}
    tr.sub-form-row.hide{display:none}
    .name-link{color:#5B65F3;cursor:pointer;text-decoration:none;font-weight:500;transition:color .2s}
    .name-link:hover{color:#4F46E5;text-decoration:underline}
    .expand-btn{display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;background:#e5e7eb;color:#374151;border:none;cursor:pointer;font-size:12px;font-weight:700;margin-right:6px;vertical-align:middle;border-radius:4px;transition:all .2s}
    .expand-btn:hover{background:#5B65F3;color:#fff}
    .expand-btn.open{background:#5B65F3;color:#fff;transform:rotate(90deg)}
    .add-sub-btn{display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;background:#10b981;color:#fff;border:none;cursor:pointer;font-size:16px;font-weight:700;vertical-align:middle;border-radius:4px;transition:all .2s}
    .add-sub-btn:hover{background:#059669}
    .id-cell{display:flex;align-items:center;gap:4px}
    .act-btns{display:flex;gap:6px;flex-wrap:wrap}
    .act-btns button{padding:6px 12px;font-size:13px;border-radius:6px}
    .badge{display:inline-block;padding:4px 10px;font-size:12px;font-weight:600;white-space:nowrap;border-radius:6px}
    .b-klang{background:#d1fae5;color:#065f46;border:1px solid #6ee7b7}
    .b-asset{background:#ede9fe;color:#5b21b6;border:1px solid #c4b5fd}
    .b-3e{background:#dbeafe;color:#1d4ed8;border:1px solid #93c5fd}
    .b-3in{background:#d1fae5;color:#065f46;border:1px solid #6ee7b7}
    .b-3em{background:#ede9fe;color:#5b21b6;border:1px solid #c4b5fd}
    .b-3el{background:#fef9c3;color:#713f12;border:1px solid #fde047}
    .b-hd{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5}
    .b-ep{background:#fce7f3;color:#9d174d;border:1px solid #f9a8d4}
    .b-3p{background:#f0fdf4;color:#14532d;border:1px solid #86efac}
    .b-all{background:#f3f4f6;color:#374151;border:1px solid #d1d5db}
    .badge-wrap{display:flex;flex-direction:column;gap:4px}
    #paging{display:flex;justify-content:center;align-items:center;gap:12px;padding:16px;margin-top:8px}
    .pg-btn{padding:10px 20px;font-size:14px;font-weight:600;cursor:pointer;font-family:'Sarabun',sans-serif;background:#fff;border:1px solid #d1d5db;border-radius:8px;color:#374151;transition:all .2s}
    .pg-btn:hover{background:#f9fafb;border-color:#9ca3af;color:#111827}
    .pg-info{font-weight:600;font-size:14px;color:#374151;padding:10px 16px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px}
    ul.ac{list-style:none;margin:4px 0 0;padding:0;background:#fff;border:1px solid #e5e7eb;max-height:180px;overflow-y:auto;position:fixed;z-index:3000;box-shadow:0 10px 25px rgba(0,0,0,.1);min-width:180px;border-radius:8px}
    ul.ac li{padding:10px 14px;cursor:pointer;font-size:13px;color:#374151;transition:background .2s}
    ul.ac li:hover{background:#EEF2FF;color:#5B65F3}
    .tx-ov{position:fixed;inset:0;background:rgba(0,0,0,.5);display:none;justify-content:center;align-items:center;z-index:5000;backdrop-filter:blur(4px);padding:12px}
    .tx-ov.on{display:flex}
    .tx-modal{background:#fff;border:none;width:95vw;max-width:1400px;height:90vh;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,.15);border-radius:12px;overflow:hidden}
    .tx-head{display:flex;align-items:center;justify-content:space-between;padding:16px 24px;background:#f9fafb;border-bottom:1px solid #e5e7eb;color:#111827}
    .tx-head h3{font-size:16px;font-weight:700;margin:0}
    .tx-badge{background:#EEF2FF;color:#5B65F3;padding:4px 12px;font-size:13px;font-weight:700;border-radius:6px;border:1px solid #C7D2FE}
    .tx-xbtn{background:#e5e7eb;color:#374151;border:none;width:32px;height:32px;cursor:pointer;font-size:16px;font-weight:700;border-radius:6px;display:flex;align-items:center;justify-content:center;transition:all .2s}
    .tx-xbtn:hover{background:#ef4444;color:#fff}
    .tx-body{flex:1;overflow:auto;background:#fff}
    .tx-foot{padding:12px 24px;border-top:1px solid #e5e7eb;text-align:right;color:#6b7280;font-size:13px;background:#f9fafb}
    .tx-spin{display:flex;justify-content:center;align-items:center;padding:50px}
    .tx-spin-inner{width:36px;height:36px;border:4px solid rgba(91,101,243,.1);border-top:4px solid #5B65F3;border-radius:50%;animation:sp .8s linear infinite}
    @keyframes sp{to{transform:rotate(360deg)}}
    .tx-tbl{width:100%;border-collapse:collapse;font-size:13px}
    .tx-tbl thead{background:#f9fafb;position:sticky;top:0;z-index:5}
    .tx-tbl th{color:#374151;padding:12px 16px;text-align:left;font-weight:600;font-size:13px;white-space:nowrap;border:1px solid #e5e7eb;border-top:none}
    .tx-tbl th:first-child{border-left:none}
    .tx-tbl th:last-child{border-right:none}
    .tx-tbl td{padding:10px 16px;border:1px solid #e5e7eb;color:#1f2937;font-size:13px;vertical-align:middle}
    .tx-tbl td:first-child{border-left:none}
    .tx-tbl td:last-child{border-right:none}
    .tx-tbl tbody tr:hover{background:#f9fafb}
    .tx-type{display:block;width:100%;height:100%;text-align:center;padding:12px 8px;font-weight:600;color:#fff;font-size:12px}
    .t-in{background:#10b981}.t-ret{background:#06b6d4}.t-sell{background:#ef4444}.t-bor{background:#f59e0b}.t-wit{background:#f97316}
    .tx-empty{text-align:center;padding:50px;color:#9ca3af;font-size:16px}
    .finput{padding:8px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;background:#fff;font-family:'Sarabun',sans-serif;width:100%;transition:all .2s}
    .finput:focus{outline:none;border-color:#5B65F3;box-shadow:0 0 0 3px rgba(91,101,243,.1)}
    .toast{position:fixed;bottom:24px;right:24px;padding:12px 24px;font-size:14px;font-weight:600;z-index:9999;color:#fff;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,.15);opacity:0;transition:opacity .3s}
    @media(max-width:768px){.abar{flex-direction:column} .abar input,.abar select{min-width:100%!important;max-width:100%!important} #content{padding:80px 8px 16px}}
    .btn-home{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding:10px 20px;
      font-family:'Sarabun',sans-serif;
      font-size:14px;
      font-weight:600;
      color:#fff;
      text-decoration:none;
      cursor:pointer;
      white-space:nowrap;
      background:#5B65F3;
      border-radius:8px;
      box-shadow:0 1px 3px rgba(91,101,243,.3);
      transition:all .2s;
    }
    .btn-home:hover{
      background:#4F46E5;
      box-shadow:0 4px 6px rgba(91,101,243,.2);
      transform:translateY(-1px);
    }
    .btn-home:active{
      transform:translateY(0);
    }
    /* สถานะ auto-refresh มุมขวาบน */
    .live-badge{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:#059669;background:#d1fae5;border:1px solid #6ee7b7;padding:4px 10px;border-radius:999px}
    .live-dot{width:8px;height:8px;border-radius:50%;background:#10b981;animation:pulse 1.5s infinite}
    @keyframes pulse{0%,100%{opacity:1}50%{opacity:.35}}
    /* ---- ปุ่มจัดการบนหลังการ์ด ---- */
    .vb-admin{display:flex;gap:6px;flex-wrap:wrap;justify-content:center;margin-top:12px}
    .vb-admin .btn{display:inline-flex;align-items:center;gap:4px;padding:6px 10px;font-size:12px;border-radius:8px}
    .btn-sub{background:#fff;color:#059669;border:1px solid #6ee7b7}
    .btn-sub:hover{background:#ecfdf5}
    /* ---- ตัวโหลดเล็กข้างหัวข้อ (ตอนค้นหา/รีเฟรชเงียบ) ---- */
    .mini-load{display:none;align-items:center;gap:6px;font-size:13px;font-weight:500;color:#6b7280;margin-left:10px;vertical-align:middle;letter-spacing:0}
    .mini-load.on{display:inline-flex}
    .mini-load i{width:14px;height:14px;border:2px solid #e5e7eb;border-top-color:#5B65F3;border-radius:50%;animation:sp .8s linear infinite}
    /* ---- ฟอร์มเพิ่ม/แก้ไข และยืนยันลบ ---- */
    .fm-ov{position:fixed;inset:0;background:rgba(17,24,39,.5);display:none;align-items:center;justify-content:center;z-index:5500;padding:16px;backdrop-filter:blur(3px)}
    .fm-ov.on{display:flex}
    .fm-modal{background:#fff;border-radius:16px;width:100%;max-width:640px;max-height:92vh;display:flex;flex-direction:column;box-shadow:0 24px 60px rgba(0,0,0,.2);overflow:hidden;animation:fmIn .2s ease-out}
    .fm-modal.fm-sm{max-width:460px}
    @keyframes fmIn{from{opacity:0;transform:translateY(8px) scale(.98)}to{opacity:1;transform:none}}
    .fm-head{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;padding:20px 24px;border-bottom:1px solid #e5e7eb}
    .fm-head h3{font-size:18px;font-weight:700;color:#111827}
    .fm-head p{font-size:13px;color:#6b7280;margin-top:2px;word-break:break-word}
    .fm-body{padding:20px 24px;display:grid;grid-template-columns:1fr 1fr;gap:14px 16px;overflow-y:auto}
    .fm-body.fm-one{grid-template-columns:1fr}
    .fm-field{display:flex;flex-direction:column;gap:6px;min-width:0}
    .fm-field>span{font-size:13px;font-weight:600;color:#374151}
    .fm-field b{color:#ef4444}
    .fm-full{grid-column:1/-1}
    .fm-field .finput{padding:10px 12px;font-size:14px}
    textarea.finput{resize:vertical}
    .fm-hint{grid-column:1/-1;font-size:12px;color:#6b7280;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px}
    .fm-hint:empty{display:none}
    .fm-foot{display:flex;justify-content:flex-end;gap:10px;padding:16px 24px;border-top:1px solid #e5e7eb;background:#f9fafb}
    .fm-foot .btn:disabled{opacity:.6;cursor:not-allowed}
    .del-warn{font-size:13px;line-height:1.6;color:#92400e;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:10px 12px;white-space:pre-line}
    @media(max-width:560px){.fm-body{grid-template-columns:1fr}}
    /* ---- ตัวเลือกรูปสินค้า ---- */
    .img-pick{position:relative;height:190px;border:2px dashed #d1d5db;border-radius:12px;background:#f9fafb;cursor:pointer;overflow:hidden;display:flex;align-items:center;justify-content:center;transition:border-color .2s,background .2s;outline:none}
    .img-pick:hover,.img-pick:focus-visible,.img-pick.drag{border-color:#5B65F3;background:#EEF2FF}
    .img-pick img{width:100%;height:100%;object-fit:contain;display:none;background:#fff}
    .img-pick.has img{display:block}
    .img-pick.has{border-style:solid}
    .img-empty{display:flex;flex-direction:column;align-items:center;gap:4px;color:#9ca3af;text-align:center;padding:12px}
    .img-empty strong{font-size:14px;color:#4b5563;font-weight:600}
    .img-empty small{font-size:12px}
    .img-pick.has .img-empty{display:none}
    .img-busy{position:absolute;inset:0;background:rgba(255,255,255,.85);display:none;flex-direction:column;align-items:center;justify-content:center;gap:8px;font-size:13px;font-weight:600;color:#374151}
    .img-busy i{width:28px;height:28px;border:3px solid #e5e7eb;border-top-color:#5B65F3;border-radius:50%;animation:sp .8s linear infinite}
    .img-pick.busy .img-busy{display:flex}
    .img-acts{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
    .img-acts .btn{padding:7px 14px;font-size:13px}
    .img-acts .img-del{color:#dc2626}
    .img-acts .img-del[hidden]{display:none}
    .img-note{font-size:12px;color:#6b7280}
  </style>
</head>
<body>

<!-- Overlay with Block Loading -->
<div class="ov" id="ov">
  <div class="progress-container">
    <p class="ov-text" id="ovText">กำลังโหลดข้อมูล...</p>
    <div class="progress-track">
      <div class="progress-fill" id="progressBar"></div>
    </div>
    <p class="ov-percent" id="ovPercent">0%</p>
  </div>
</div>

<div class="toast" id="toast"></div>

<div class="tx-ov" id="txOv">
  <div class="tx-modal">
    <div class="tx-head"><div style="display:flex;align-items:center;gap:12px"><h3>ประวัติ Transaction</h3><span class="tx-badge" id="txId">-</span><span id="txName" style="font-size:14px;color:#6b7280"></span></div><button class="tx-xbtn" onclick="closeTx()">&#10005;</button></div>
    <div class="tx-body" id="txBody"><div class="tx-spin"><div class="tx-spin-inner"></div></div></div>
    <div class="tx-foot" id="txFoot">กำลังโหลด...</div>
  </div>
</div>

<!-- ฟอร์มเพิ่ม/แก้ไข/เพิ่มรายการย่อย -->
<div class="fm-ov" id="fmOv" onclick="if(event.target===this)closeItemForm()">
  <form class="fm-modal" onsubmit="event.preventDefault();submitItemForm()" autocomplete="off">
    <div class="fm-head"><div><h3 id="fmTitle">เพิ่มสินค้าใหม่</h3><p id="fmSub"></p></div><button type="button" class="tx-xbtn" onclick="closeItemForm()">&#10005;</button></div>
    <div class="fm-body">
      <div class="fm-field fm-full">
        <span>รูปสินค้า</span>
        <div class="img-pick" id="imgPick" tabindex="0" role="button" aria-label="เลือกรูปสินค้า">
          <img id="imgPrev" alt="">
          <div class="img-empty"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg><strong>คลิก ลาก หรือวาง (Ctrl+V) รูปที่นี่</strong><small>JPG / PNG · ระบบย่อขนาดให้อัตโนมัติ</small></div>
          <div class="img-busy" id="imgBusy"><i></i><span>กำลังอัปโหลดรูป...</span></div>
        </div>
        <div class="img-acts">
          <button type="button" class="btn btn-clr" onclick="$('fImg').click()" id="imgChooseBtn">เลือกรูป</button>
          <button type="button" class="btn btn-clr img-del" id="imgDelBtn" onclick="clearPickedImage()">ลบรูป</button>
          <span class="img-note" id="imgNote"></span>
        </div>
        <input type="file" id="fImg" accept="image/*" hidden>
      </div>
      <label class="fm-field fm-full"><span>ชื่อสินค้า <b>*</b></span><input id="fName" class="finput" maxlength="255" placeholder="เช่น หลอดไฟ LED 9W"></label>
      <label class="fm-field"><span>บริษัท <b>*</b></span><select id="fPriv" class="finput"></select></label>
      <label class="fm-field"><span>ประเภท</span><select id="fType" class="finput"><option value="คลัง">คลัง</option><option value="ทรัพย์สินบริษัท">ทรัพย์สินบริษัท</option></select></label>
      <label class="fm-field"><span>ยี่ห้อ</span><input id="fBrand" class="finput" list="dlBrands" placeholder="พิมพ์หรือเลือก"></label>
      <label class="fm-field"><span>สถานที่เก็บ</span><input id="fLoc" class="finput" list="dlLocs" placeholder="พิมพ์หรือเลือก"></label>
      <label class="fm-field"><span>หมวดหมู่</span><input id="fCat" class="finput"></label>
      <label class="fm-field" id="fQtyWrap"><span>จำนวน</span><input id="fQty" type="number" step="1" class="finput"></label>
      <p class="fm-hint" id="fmHint"></p>
    </div>
    <div class="fm-foot"><button type="button" class="btn btn-can" onclick="closeItemForm()">ยกเลิก</button><button type="submit" class="btn btn-save" id="fmSave">บันทึก</button></div>
  </form>
</div>
<datalist id="dlBrands"></datalist>
<datalist id="dlLocs"></datalist>

<!-- ยืนยันการลบ -->
<div class="fm-ov" id="delOv" onclick="if(event.target===this)closeDelete()">
  <form class="fm-modal fm-sm" onsubmit="event.preventDefault();confirmDelete()">
    <div class="fm-head"><div><h3>ลบสินค้า</h3><p id="delSub"></p></div><button type="button" class="tx-xbtn" onclick="closeDelete()">&#10005;</button></div>
    <div class="fm-body fm-one">
      <div class="del-warn" id="delWarn"></div>
      <label class="fm-field"><span>เหตุผลในการลบ <b>*</b></span><textarea id="delReason" class="finput" rows="3" placeholder="เช่น สร้างซ้ำ, เลิกใช้งาน"></textarea></label>
    </div>
    <div class="fm-foot"><button type="button" class="btn btn-can" onclick="closeDelete()">ยกเลิก</button><button type="submit" class="btn btn-del" id="delBtn">ลบ</button></div>
  </form>
</div>

<div class="sb-ov" id="sbOv" onclick="closeSB()"></div>
<div class="sidebar" id="sidebar">
 <div class="sb-head"><img src="https://lh3.googleusercontent.com/d/1qruaZSyb6gXrJ1Bc_l-p50LdZ6mszbE0" alt="Logo"><span>3E TRADING</span><button class="sb-close" onclick="closeSB()">&#10005;</button></div>
  <div class="sb-nav">
    <div class="sb-sec">เมนูหลัก</div>
    <a class="sb-item" target="_blank" href="{{ route('inventory.transaction', ['create_by' => $authUser['name'] ?? '']) }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>รายการสินค้า เข้า-ออก</a>
    <a class="sb-item cur" target="_blank" href="{{ route('inventory.item', ['create_by' => $authUser['name'] ?? '']) }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>ค้นหาสินค้า</a>
  </div>
</div>

<div class="topbar">
  <button class="hamburger" onclick="openSB()"><span></span><span></span><span></span></button>
  <img src="https://lh3.googleusercontent.com/d/1qruaZSyb6gXrJ1Bc_l-p50LdZ6mszbE0" alt="Logo" class="topbar-logo">
  <span class="topbar-title">3E TRADING</span>
  <div class="topbar-right">
    <span class="topbar-name"> ผู้ใช้: {{ $authUser['name'] ?? '' }}</span>
    <span class="topbar-badge">{{ strtoupper($authRole) }}</span>
    <a href="http://server_update:8000/solist" class="btn-home">หน้าหลัก</a>
  </div>
</div>

<div id="content">
  <div class="card">
    <h2>ค้นหาสินค้า <span class="mini-load" id="miniLoad"><i></i>กำลังโหลด...</span></h2>
    <div class="abar">
      @if(in_array($authRole, ['admin','user']))
        <button class="btn btn-add" onclick="addRow()">+ เพิ่มสินค้าใหม่</button>
      @endif
      <input type="text" id="sName" placeholder="ชื่อสินค้า..." style="flex:2;min-width:200px" oninput="debounceFilter()">
      <input type="text" id="sBrand" placeholder="ยี่ห้อ..." oninput="debounceFilter()">
      <select id="sPriv" onchange="applyFilter()"><option value="">ทุกบริษัท</option><option value="3E">3E</option><option value="3IN">3IN</option><option value="3EM">3EM</option><option value="3EL">3EL</option><option value="HD">HD</option><option value="EP">EP</option><option value="3P">3P</option><option value="AE&T">AE&T</option></select>
      <button class="btn btn-clr" onclick="clearFilter()">ล้างตัวกรอง</button>
    </div>
  </div>
  <div class="v-grid" id="tb"></div>
  <div id="paging"></div>
</div>

<script>
const CSRF=document.querySelector('meta[name="csrf-token"]').content;
const ROLE=@json($authRole);
const USER_NAME=@json($authUser['name'] ?? '');
const NEST_URL=@json($nestUrl);
const NEST_KEY=@json($nestKey);
// สิทธิ์ให้ตรงกับฝั่ง server: เพิ่ม = admin/user (guardRole) · แก้ไข/ลบ = admin หรือ ชัย (guardEditDelete)
const CAN_ADD=(ROLE==='admin'||ROLE==='user');
const CAN_EDIT=(ROLE==='admin'||String(USER_NAME||'').trim().toLowerCase()==='ชัย');
const COMPANIES=[{code:'3E',label:'Triple E Trading'},{code:'3IN',label:'Triple E Innovation'},{code:'3EM',label:'Triple E Empire Group'},{code:'3EL',label:'Triple E Lighting'},{code:'HD',label:'Hikari Denki'},{code:'EP',label:'Eita & Paul'},{code:'3P',label:'Triple P Factory & Eng'},{code:'AE&T',label:'AE&T International'}];
const PM={'3E':'b-3e','3IN':'b-3in','3EM':'b-3em','3EL':'b-3el','HD':'b-hd','EP':'b-ep','3P':'b-3p'};
const TM={'รับเข้าสต็อก':'t-in','คืนเข้าสต็อก':'t-ret','ขายสินค้าออก':'t-sell','ยืมสินค้า':'t-bor','เบิกของ':'t-wit'};

// ไอคอน SVG ที่ใช้ในการ์ด
const svgI=(d,s=16,fill='none')=>`<svg width="${s}" height="${s}" viewBox="0 0 24 24" fill="${fill}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${d}</svg>`;
const ICON={
  box:(s)=>svgI('<path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>',s),
  tag:(s)=>svgI('<path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>',s),
  home:(s)=>svgI('<path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',s),
  spark:(s)=>svgI('<path d="M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9z"/><path d="M19 3v4M17 5h4"/>',s),
  hash:(s)=>svgI('<line x1="4" y1="9" x2="20" y2="9"/><line x1="4" y1="15" x2="20" y2="15"/><line x1="10" y1="3" x2="8" y2="21"/><line x1="16" y1="3" x2="14" y2="21"/>',s),
  arrow:(s)=>svgI('<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>',s),
  bolt:(s)=>svgI('<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>',s,'currentColor'),
  image:(s)=>svgI('<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>',s),
  plus:(s)=>svgI('<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>',s),
  edit:(s)=>svgI('<path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 013 3L7 19l-4 1 1-4z"/>',s),
  trash:(s)=>svgI('<polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/>',s)
};

// เรียก API แล้ว "โยน error" เมื่อ server ตอบไม่สำเร็จ (เดิมไม่เช็ค ทำให้ขึ้น "สำเร็จ" ทั้งที่ล้มเหลว)
async function apiRequest(method,url,data){
  const opt={method,cache:'no-store',headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF,'Cache-Control':'no-cache, no-store, must-revalidate'}};
  if(data!==undefined){opt.headers['Content-Type']='application/json';opt.body=JSON.stringify(data);}
  if(method==='GET'){url+=(url.includes('?')?'&':'?')+'_t='+Date.now();opt.headers['Pragma']='no-cache';}
  const res=await fetch(url,opt);
  let body=null;
  try{body=await res.json();}catch(e){}
  if(!res.ok||(body&&body.success===false)){
    const msg=(body&&(body.error||body.message))
      ||(res.status===403?'ไม่มีสิทธิ์ทำรายการนี้'
        :res.status===419?'เซสชันหมดอายุ กรุณารีเฟรชหน้า'
        :res.status===401?'กรุณาเข้าสู่ระบบใหม่'
        :'เกิดข้อผิดพลาด ('+res.status+')');
    throw new Error(msg);
  }
  return body;
}
const API={
  get:(u)=>apiRequest('GET',u),
  post:(u,d)=>apiRequest('POST',u,d),
  put:(u,d)=>apiRequest('PUT',u,d),
  del:(u)=>apiRequest('DELETE',u)
};

const HIDE_IDITEMS=['3E-000013']; // ID ที่แสดงซ้อน/ซ้ำ ไม่ต้องแสดง (รายการอื่นแสดงตามปกติ)
let uBrands=[],uLocs=[],products=[],subs={},pg=1,totalItems=0;
let allProducts=[],allSubs={}; // ข้อมูลทั้งหมดที่โหลดมาจาก server แล้วแบ่งหน้าแสดงผลฝั่ง client
const PG=50;
let filterTimeout=null;
let currentFilters={name:'',brand:'',location:'',priv:'',type:''};
let progressInterval=null,ovProg=0;
let renderSeq=0; // กันผลโหลดเก่ามาทับผลใหม่ (เช่น พิมพ์ค้นหาเร็วๆ)
const $=(id)=>document.getElementById(id);

/* ========== Overlay โหลด (แถบ % ตามจริง) ========== */
function ovIsOn(){return $('ov').classList.contains('on')}
function showOv(t){
  $('ovText').textContent=t||'กำลังโหลดข้อมูล...';
  $('ov').classList.add('on');
  startProgressSimulation();
}
function setOvBar(p){
  $('progressBar').style.width=Math.floor(p)+'%';
  $('ovPercent').textContent=Math.floor(p)+'%';
}
// ช่วงโหลดข้อมูลรายการ: ขยับเองไม่เกิน 40% (ส่วนที่เหลือเดินตามจำนวนการ์ดที่โหลดรูปเสร็จจริง)
function startProgressSimulation(){
  ovProg=0;setOvBar(0);
  clearInterval(progressInterval);
  progressInterval=setInterval(()=>{
    ovProg+=ovProg<25?Math.random()*8:Math.random()*1.5;
    if(ovProg>40) ovProg=40;
    setOvBar(ovProg);
  },150);
}
function setOvProgress(p,t){
  if(!ovIsOn()) return;
  clearInterval(progressInterval);
  ovProg=Math.max(ovProg,Math.min(100,p));
  setOvBar(ovProg);
  if(t) $('ovText').textContent=t;
}
function hideOv(){
  clearInterval(progressInterval);
  setOvBar(100);
  setTimeout(()=>{
    $('ov').classList.remove('on');
    setTimeout(()=>{ovProg=0;setOvBar(0);},300);
  },200);
}
function setMiniLoad(on){$('miniLoad')?.classList.toggle('on',!!on)}

function openSB(){$('sidebar').classList.add('open');$('sbOv').classList.add('open')}
function closeSB(){$('sidebar').classList.remove('open');$('sbOv').classList.remove('open')}
function toast(m,e){let t=$('toast');t.textContent=m;t.style.background=e?'#ef4444':'#10b981';t.style.opacity='1';clearTimeout(t._t);t._t=setTimeout(()=>t.style.opacity='0',3000)}
function ej(s){return(s||'').replace(/\\/g,'\\\\').replace(/'/g,"\\'").replace(/"/g,'&quot;').replace(/\n/g,'\\n')}
function eh(s){return String(s??'').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;')}
function ci(s){return(s||'').replace(/[^a-zA-Z0-9]/g,'_')}
const sleep=(ms)=>new Promise(r=>setTimeout(r,ms));

function debounceFilter(){
  clearTimeout(filterTimeout);
  filterTimeout=setTimeout(applyFilter,400);
}

/* ========== โหลดข้อมูลรายการ ========== */
// server จำกัด limit สูงสุด 200 ต่อครั้ง (getPagedItems) จึงต้องดึงทีละหน้าให้ครบทุกหน้า
async function fetchItemsAllPages(){
  const LIMIT=200;
  const base={
    limit:LIMIT,
    name:currentFilters.name||'',
    brand:currentFilters.brand||'',
    location:currentFilters.location||'',
    priv:currentFilters.priv||'',
    type:currentFilters.type||''
  };
  const url=(p)=>'/api/vehicles-items?'+new URLSearchParams({...base,page:p}).toString();
  const first=await API.get(url(1));
  const last=Math.max(1,parseInt(first.lastPage)||1);
  let data=[...(first.data||[])];
  if(last>1){
    const rest=await Promise.all(Array.from({length:last-1},(_,i)=>API.get(url(i+2))));
    rest.forEach(r=>{data=data.concat(r.data||[])});
  }
  return {data,subs:first.subs||{},brands:first.brands||[],locations:first.locations||[]};
}

function fillDatalists(){
  const fill=(id,arr)=>{const dl=$(id);if(!dl)return;dl.innerHTML=[...new Set(arr.filter(Boolean))].map(v=>`<option value="${eh(v)}"></option>`).join('')};
  fill('dlBrands',uBrands);
  fill('dlLocs',uLocs);
}

// โหลดข้อมูลทั้งหมด (ตามตัวกรองปัจจุบัน) แล้วแบ่งหน้าแสดงผลฝั่ง client
// keepPage=true ใช้ตอน refresh เงียบๆ (SSE, บันทึก/ลบ) ให้อยู่หน้าเดิมที่ผู้ใช้ดูอยู่
async function fetchAllAndRender(showLoader=true,keepPage=false){
  const seq=++renderSeq;
  if(showLoader) showOv(); else setMiniLoad(true);
  try{
    const r=await fetchItemsAllPages();
    if(seq!==renderSeq) return;
    allProducts=r.data;
    allSubs=r.subs;
    totalItems=allProducts.length;
    uBrands=r.brands;uLocs=r.locations;fillDatalists();
    const maxPg=Math.max(1,Math.ceil(totalItems/PG));
    await renderPage(keepPage?Math.min(pg,maxPg):1,seq);
  }catch(e){
    if(seq===renderSeq){console.error('Error loading data:',e);toast('โหลดข้อมูลล้มเหลว: '+e.message,true);}
  }finally{
    if(seq===renderSeq){if(ovIsOn()) hideOv();setMiniLoad(false);}
  }
}

// เปลี่ยนหน้า: โหลดรูปของหน้าใหม่ให้เสร็จก่อนค่อยแสดง
async function goPage(p){
  const seq=++renderSeq;
  showOv(`กำลังโหลดหน้า ${p}...`);
  try{
    const ok=await renderPage(p,seq);
    if(ok) scrollTo(0,0);
  }finally{
    if(seq===renderSeq&&ovIsOn()) hideOv();
  }
}

async function renderPage(page,seq){
  pg=page;
  const start=(pg-1)*PG;
  products=allProducts.slice(start,start+PG);
  subs={};
  products.forEach(p=>{
    const key=p._pid||p.iditem;
    subs[key]=allSubs[key]||[];
  });
  return render(seq);
}

function applyFilter(){
  currentFilters={
    name:($('sName').value||'').trim(),
    brand:($('sBrand').value||'').trim(),
    location:'',
    priv:$('sPriv').value,
    type:''
  };
  fetchAllAndRender(false);
}

function clearFilter(){
  ['sName','sBrand','sPriv'].forEach(id=>$(id).value='');
  currentFilters={name:'',brand:'',location:'',priv:'',type:''};
  fetchAllAndRender(true);
}

/* ========== การ์ด ========== */
// สร้างการ์ดแบบพลิก: ด้านหน้า = รูป/ชื่อ/บริษัท/รหัส, ด้านหลัง = จำนวน/ยี่ห้อ/ประเภท/หมายเหตุ/ปุ่ม
function buildCard(it,parent,isSub,parentKey){
  const card=document.createElement('div');
  card.className='v-card'+(isSub?' sub':'');
  card.tabIndex=0;

  const type=it.typeitem||parent?.typeitem||'';
  const priv=(it.privilege||parent?.privilege||'').trim();
  const comp=COMPANIES.find(c=>c.code===priv);
  const qty=parseInt(it.quantity)||0;
  const brand=it.brand||'';
  const typeLabel=type==='ทรัพย์สินบริษัท'?'ทรัพย์สิน':(type||'-');
  const idJs=ej(it.iditem);

  let actions='';
  if(CAN_ADD&&!isSub) actions+=`<button class="btn btn-sub" onclick="event.stopPropagation();openItemForm('sub','${ej(parentKey)}')">${ICON.plus(14)} รายการย่อย</button>`;
  if(CAN_EDIT) actions+=`<button class="btn btn-edit" onclick="event.stopPropagation();openItemForm('edit','${idJs}')">${ICON.edit(14)} แก้ไข</button><button class="btn btn-del" onclick="event.stopPropagation();openDelete('${idJs}')">${ICON.trash(14)} ลบ</button>`;

  card.innerHTML=`
    <div class="v-inner">
      <div class="v-face v-front">
        <div class="vf-media">
          <div class="vf-img"><div class="vf-ph">${ICON.image(28)}<span>กำลังโหลดรูป...</span></div></div>
          <span class="vf-pill${qty<=0?' zero':''}">${ICON.box(16)} คงเหลือ ${qty}</span>
          ${isSub?'<span class="vf-sub">รายการย่อย</span>':''}
        </div>
        <div class="vf-body">
          <div class="vf-text">
            <div class="vf-company">${ICON.spark(18)}<span class="vf-company-name">${eh(comp?comp.label:(priv||'ไม่ระบุบริษัท'))}</span>${priv?`<span class="badge ${PM[priv]||'b-all'}">${eh(priv)}</span>`:''}</div>
            <div class="vf-name" title="${eh(it.name)}">${eh(it.name)}</div>
            <div class="vf-id">${ICON.hash(16)}<span>${eh(it.iditem)}</span></div>
          </div>
          <div class="vf-hint"><span class="h-hover">ชี้เมาส์เพื่อดูรายละเอียด</span><span class="h-touch">แตะเพื่อดูรายละเอียด</span>${ICON.arrow(18)}</div>
        </div>
      </div>
      <div class="v-face v-back">
        <div class="vb-stats">
          <div class="vb-stat"><div class="vb-ic">${ICON.box(18)}</div><strong>${qty}</strong><small>จำนวน</small></div>
          <div class="vb-stat"><div class="vb-ic">${ICON.tag(18)}</div><strong title="${eh(brand)}">${eh(brand||'-')}</strong><small>ยี่ห้อ</small></div>
          <div class="vb-stat"><div class="vb-ic">${ICON.home(18)}</div><strong>${eh(typeLabel)}</strong><small>ประเภท</small></div>
        </div>
        <h4 class="vb-title">รายละเอียดสินค้า</h4>
        <p class="vb-note">กำลังโหลดหมายเหตุ...</p>
        <button class="vb-cta" onclick="event.stopPropagation();openTx('${idJs}','${ej(it.name)}')">${ICON.bolt(15)} ดูประวัติ Transaction</button>
        ${actions?`<div class="vb-admin">${actions}</div>`:''}
      </div>
    </div>`;

  // อุปกรณ์จอสัมผัส (ไม่มี hover) ใช้การแตะเพื่อพลิกการ์ด
  card.addEventListener('click',e=>{
    if(e.target.closest('a,button')) return;
    if(window.matchMedia('(hover: none)').matches) card.classList.toggle('flipped');
  });
  card.addEventListener('keydown',e=>{
    if(e.target===card&&(e.key==='Enter'||e.key===' ')){e.preventDefault();card.classList.toggle('flipped');}
  });
  return card;
}

// สร้างการ์ดทั้งหน้าไว้นอกจอ รอโหลด Transaction + รูปให้ครบก่อน แล้วค่อยแสดงพร้อมกันทีเดียว
async function render(seq){
  const frag=document.createDocumentFragment();
  const jobs=[];
  products.forEach(item=>{
    const key=item._pid||item.iditem;
    if(!HIDE_IDITEMS.includes(item.iditem)){
      const c=buildCard(item,null,false,key);
      frag.appendChild(c);jobs.push([c,item.iditem,item.image]);
    }
    (subs[key]||[]).forEach(sub=>{
      if(HIDE_IDITEMS.includes(sub.iditem)) return;
      const c=buildCard(sub,item,true,key);
      frag.appendChild(c);jobs.push([c,sub.iditem,sub.image]);
    });
  });

  if(!jobs.length){
    if(seq!==renderSeq) return false;
    $('tb').innerHTML=`<div style="text-align:center;padding:40px;color:#9ca3af;grid-column:1/-1">ไม่พบข้อมูลสินค้า</div>`;
    renderPg();
    return true;
  }

  const total=jobs.length;
  let done=0;
  setOvProgress(40,`กำลังโหลดรูปภาพ... (0/${total})`);
  const all=Promise.all(jobs.map(([c,id,img])=>loadCardTx(c,id,img).finally(()=>{
    done++;
    if(seq===renderSeq) setOvProgress(40+60*done/total,`กำลังโหลดรูปภาพ... (${done}/${total})`);
  })));
  // กันค้าง: ถ้ารูปบางรูปช้าผิดปกติ เกิน 30 วินาทีให้แสดงไปก่อน (รูปที่เหลือจะโผล่ตามมาเอง)
  await Promise.race([all,sleep(30000)]);

  if(seq!==renderSeq) return false;
  $('tb').replaceChildren(frag);
  renderPg();
  return true;
}

function toDirectImageUrl(url){
  if(!url) return url;
  let id=null;
  let m=url.match(/\/file\/d\/([a-zA-Z0-9_-]+)/);
  if(m) id=m[1];
  if(!id){m=url.match(/[?&]id=([a-zA-Z0-9_-]+)/);if(m) id=m[1];}
  return id?`https://drive.google.com/thumbnail?id=${id}&sz=w600`:url;
}

// รอรูปโหลด: คืนค่า 'load' | 'error' | 'timeout'
function waitImg(img,ms=20000){
  return new Promise(res=>{
    if(img.complete&&img.naturalWidth){res('load');return;}
    const t=setTimeout(()=>res('timeout'),ms);
    img.addEventListener('load',()=>{clearTimeout(t);res('load')},{once:true});
    img.addEventListener('error',()=>{clearTimeout(t);res('error')},{once:true});
  });
}

// โหลดหมายเหตุจาก transaction + แสดงรูป (รูปของสินค้าก่อน ถ้าไม่มีค่อยใช้รูปจาก Transaction)
// คืน Promise ที่เสร็จเมื่อรูปโหลดเสร็จ
async function loadCardTx(card,id,itemImage){
  const media=card.querySelector('.vf-img');
  const noteEl=card.querySelector('.vb-note');
  const placeholder=(txt)=>`<div class="vf-ph">${ICON.image(28)}<span>${txt}</span></div>`;

  let rows=[];
  try{
    rows=await API.get('/api/transaction/by-item/'+encodeURIComponent(id));
    if(!Array.isArray(rows)) rows=[];
    const notes=[...new Set(rows.map(r=>r['หมายเหตุ']).filter(Boolean))];
    noteEl.textContent=notes.length?notes.join(' / '):'ไม่มีหมายเหตุ';
    noteEl.title=notes.join(' / ');
  }catch(e){
    noteEl.textContent='-';
    if(!itemImage){media.innerHTML=placeholder('โหลดไม่สำเร็จ');return;}
  }

  const src=itemImage||rows.map(r=>r['รูปประกอบ']).find(Boolean);
  if(!src){media.innerHTML=placeholder('ไม่มีรูป');return;}

  // ไม่ใช้ lazy เพราะการ์ดยังอยู่นอกจอระหว่างรอโหลด
  const img=new Image();
  img.alt='';
  img.decoding='async';
  img.src=toDirectImageUrl(src);
  media.innerHTML='';
  media.appendChild(img);
  const st=await waitImg(img);
  if(st==='error') media.innerHTML=placeholder('โหลดรูปไม่ได้');
}

// หาสินค้าจากข้อมูลทั้งหมด (ไม่ใช่แค่หน้าปัจจุบัน)
function findItemById(id){
  const p=allProducts.find(x=>x.iditem===id);
  if(p) return {item:p,parent:null};
  for(const k in allSubs){
    const s=(allSubs[k]||[]).find(x=>x.iditem===id);
    if(s) return {item:s,parent:allProducts.find(x=>(x._pid||x.iditem)===k)||null};
  }
  return null;
}

function renderPg(){
  const tot=Math.max(1,Math.ceil(totalItems/PG));
  const el=$('paging');
  el.innerHTML='';
  if(tot<=1) return;
  if(pg>1){
    const b=document.createElement('button');
    b.className='pg-btn';
    b.textContent='← ก่อนหน้า';
    b.onclick=()=>goPage(pg-1);
    el.appendChild(b);
  }
  const s=document.createElement('span');
  s.className='pg-info';
  s.textContent=`หน้า ${pg} / ${tot} (${totalItems} รายการ)`;
  el.appendChild(s);
  if(pg<tot){
    const b=document.createElement('button');
    b.className='pg-btn';
    b.textContent='ถัดไป →';
    b.onclick=()=>goPage(pg+1);
    el.appendChild(b);
  }
}

/* ========== ประวัติ Transaction ========== */
async function openTx(id,name){
  $('txId').textContent=id;
  $('txName').textContent=name;
  $('txBody').innerHTML='<div class="tx-spin"><div class="tx-spin-inner"></div></div>';
  $('txFoot').textContent='กำลังโหลด...';
  $('txOv').classList.add('on');
  try{
    const rows=await API.get('/api/transaction/by-item/'+encodeURIComponent(id));
    rows.sort((a,b)=>{
      const p=ts=>{if(!ts) return 0;const m=ts.match(/^(\d{2})\/(\d{2})\/(\d{4})\s(\d{2}):(\d{2}):(\d{2})/);if(m) return new Date(m[3],m[2]-1,m[1],m[4],m[5],m[6]).getTime();return new Date(ts).getTime()||0};
      return p(b.Timestamp)-p(a.Timestamp);
    });
    renderTxModal(rows);
  }catch(e){
    $('txBody').innerHTML=`<div class="tx-empty">โหลดล้มเหลว</div>`;
  }
}
function renderTxModal(data){
  const body=$('txBody'),foot=$('txFoot');
  if(!data.length){
    body.innerHTML='<div class="tx-empty">ไม่พบ Transaction</div>';
    foot.textContent='ไม่มีข้อมูล';
    return;
  }
  foot.textContent=`พบ ${data.length} รายการ`;
  let h='<table class="tx-tbl"><thead><tr><th>วันที่</th><th>ผู้ดำเนินงาน</th><th>ประเภท</th><th>เอกสาร</th><th>รายการ</th><th>จำนวน</th><th>ราคา/หน่วย</th><th>ชั้นวาง</th><th>หมายเหตุ</th><th>รูป</th></tr></thead><tbody>';
  data.forEach(r=>{
    const tc=TM[r['ประเภทข้อมูล']||'']||'';
    const pic=r['รูปประกอบ']?`<a href="${r['รูปประกอบ']}" target="_blank" style="color:#5B65F3;font-size:12px;font-weight:600">ดูรูป</a>`:'-';
    h+=`<tr><td style="white-space:nowrap">${r.Timestamp||'-'}</td><td>${r['ชื่อผู้ดำเนินงาน']||'-'}</td><td style="padding:0"><span class="tx-type ${tc}">${r['ประเภทข้อมูล']||'-'}</span></td><td>${r['หมายเลขเอกสาร']||'-'}</td><td>${r['รายการ']||'-'}</td><td>${r['จำนวน']!==''?r['จำนวน']:'-'}</td><td>${ROLE==='viewer'?'-':(r['ราคาต่อหน่วย']!==''?r['ราคาต่อหน่วย']:'-')}</td><td>${r['ชั้นวาง']||'-'}</td><td>${r['หมายเหตุ']||'-'}</td><td>${pic}</td></tr>`;
  });
  h+='</tbody></table>';
  body.innerHTML=h;
}
function closeTx(){
  $('txOv').classList.remove('on');
  flushItemsPendingRefresh();
}

/* ========== ฟอร์ม เพิ่ม / แก้ไข / เพิ่มรายการย่อย ========== */
let fmState=null; // {mode:'add'|'edit'|'sub', id}

(function initFormSelects(){
  $('fPriv').innerHTML='<option value="" disabled selected>-- เลือกบริษัท --</option>'+COMPANIES.map(c=>`<option value="${eh(c.code)}">${eh(c.code)} · ${eh(c.label)}</option>`).join('');
})();

function addRow(){openItemForm('add')} // ปุ่ม "+ เพิ่มสินค้าใหม่" ด้านบนเรียกฟังก์ชันนี้

function openItemForm(mode,id){
  if(mode==='edit'?!CAN_EDIT:!CAN_ADD) return;
  let it={},title='เพิ่มสินค้าใหม่',sub='รหัสสินค้าจะสร้างให้อัตโนมัติ',hint='จำนวนเริ่มต้นเป็น 0 — เพิ่มจำนวนผ่านหน้า "รายการสินค้า เข้า-ออก"';
  if(mode==='edit'){
    const f=findItemById(id);
    if(!f){toast('ไม่พบสินค้านี้ อาจถูกลบไปแล้ว',true);return;}
    it=f.item;title='แก้ไขสินค้า';sub=it.iditem;
    hint='ปกติจำนวนจะเปลี่ยนตาม Transaction — แก้ช่องจำนวนเฉพาะกรณีต้องปรับยอดเท่านั้น';
  }else if(mode==='sub'){
    const parent=allProducts.find(p=>(p._pid||p.iditem)===id);
    if(!parent){toast('ไม่พบสินค้าหลัก',true);return;}
    it={...parent,name:'',image:''};title='เพิ่มรายการย่อย';sub=`ภายใต้ ${id} · ${parent.name}`;
  }
  fmState={mode,id};
  $('fmTitle').textContent=title;
  $('fmSub').textContent=sub;
  $('fmHint').textContent=hint;
  $('fName').value=it.name||'';
  $('fPriv').value=(it.privilege||'').trim();
  if($('fPriv').selectedIndex<0) $('fPriv').value='';
  $('fType').value=it.typeitem==='ทรัพย์สินบริษัท'?'ทรัพย์สินบริษัท':'คลัง';
  $('fBrand').value=it.brand||'';
  $('fLoc').value=(it.location&&it.location!=='-')?it.location:'';
  $('fCat').value=it.category||'';
  $('fQty').value=parseInt(it.quantity)||0;
  $('fQtyWrap').style.display=mode==='edit'?'':'none';
  resetImagePicker(mode==='edit'?(it.image||''):'');
  $('fmSave').disabled=false;
  $('fmSave').textContent=mode==='edit'?'บันทึกการแก้ไข':'บันทึก';
  $('fmOv').classList.add('on');
  setTimeout(()=>$('fName').focus(),50);
}

function closeItemForm(skipFlush){
  $('fmOv').classList.remove('on');
  fmState=null;
  if(!skipFlush) flushItemsPendingRefresh();
}

async function submitItemForm(){
  if(!fmState) return;
  const name=$('fName').value.trim(),priv=$('fPriv').value;
  if(!name){toast('กรุณากรอกชื่อสินค้า',true);$('fName').focus();return;}
  if(!priv){toast('กรุณาเลือกบริษัท',true);$('fPriv').focus();return;}
  const payload={
    name,
    privilege:priv,
    typeitem:$('fType').value,
    brand:$('fBrand').value.trim(),
    location:$('fLoc').value.trim(),
    category:$('fCat').value.trim() // ต้องส่งเสมอ ไม่งั้น server จะล้างหมวดหมู่เป็นค่าว่างตอนแก้ไข
  };
  const btn=$('fmSave'),label=btn.textContent;
  btn.disabled=true;
  try{
    const {mode,id}=fmState;
    // อัปโหลดรูปใหม่ (ถ้ามี) ขึ้น Google Drive ก่อน แล้วเก็บ URL ไว้กับสินค้า
    let image=imgState.url;
    if(imgState.dataUrl){
      btn.textContent='กำลังอัปโหลดรูป...';
      $('imgPick').classList.add('busy');
      try{
        const up=await API.post('/api/items/upload-image',{image:imgState.dataUrl,fileName:'item_'+Date.now()});
        if(!up||!up.url) throw new Error('อัปโหลดรูปไม่สำเร็จ');
        image=up.url;
        imgState.url=image;imgState.dataUrl=null; // กดบันทึกซ้ำจะไม่อัปโหลดซ้ำ
      }finally{
        $('imgPick').classList.remove('busy');
      }
    }
    payload.image=image;
    btn.textContent='กำลังบันทึก...';
    if(mode==='add'){
      await API.post('/api/items',{...payload,quantity:'0'});
      toast('เพิ่มสินค้าเรียบร้อย');
    }else if(mode==='sub'){
      await API.post('/api/items/sub',{...payload,parentId:id,quantity:'0'});
      toast('เพิ่มรายการย่อยเรียบร้อย');
    }else{
      const q=parseInt($('fQty').value,10);
      await API.put('/api/items/'+encodeURIComponent(id),{...payload,quantity:isNaN(q)?0:q});
      toast('บันทึกการแก้ไขเรียบร้อย');
    }
    closeItemForm(true);
    itemsRefreshPending=false;
    await fetchAllAndRender(false,true);
  }catch(e){
    toast(e.message||'บันทึกไม่สำเร็จ',true);
  }finally{
    btn.disabled=false;btn.textContent=label;
  }
}

/* ========== ตัวเลือกรูปสินค้า ========== */
let imgState={url:'',dataUrl:null}; // url = รูปที่บันทึกไว้แล้ว, dataUrl = รูปใหม่ที่เพิ่งเลือก (ยังไม่อัปโหลด)

function showImgPreview(src){
  const pick=$('imgPick'),prev=$('imgPrev');
  if(src){prev.src=src;pick.classList.add('has');}
  else{prev.removeAttribute('src');pick.classList.remove('has');}
  $('imgDelBtn').hidden=!src;
  $('imgChooseBtn').textContent=src?'เปลี่ยนรูป':'เลือกรูป';
}
function resetImagePicker(url){
  imgState={url:url||'',dataUrl:null};
  $('fImg').value='';
  $('imgNote').textContent='';
  showImgPreview(url?toDirectImageUrl(url):'');
}
function clearPickedImage(){
  imgState={url:'',dataUrl:null};
  $('fImg').value='';
  $('imgNote').textContent='';
  showImgPreview('');
}
// ย่อรูปฝั่งเครื่องก่อนอัปโหลด (ด้านยาวสุด 1600px, JPEG) ให้อัปโหลดเร็วและไม่เปลือง Drive
function resizeImage(file,max=1600,quality=0.85){
  return new Promise((resolve,reject)=>{
    const u=URL.createObjectURL(file);
    const img=new Image();
    img.onload=()=>{
      const sc=Math.min(1,max/Math.max(img.naturalWidth,img.naturalHeight));
      const w=Math.max(1,Math.round(img.naturalWidth*sc)),h=Math.max(1,Math.round(img.naturalHeight*sc));
      const c=document.createElement('canvas');c.width=w;c.height=h;
      const ctx=c.getContext('2d');ctx.fillStyle='#fff';ctx.fillRect(0,0,w,h);ctx.drawImage(img,0,0,w,h);
      URL.revokeObjectURL(u);
      resolve(c.toDataURL('image/jpeg',quality));
    };
    img.onerror=()=>{URL.revokeObjectURL(u);reject(new Error('อ่านไฟล์รูปไม่ได้ ลองใช้ไฟล์ JPG หรือ PNG'))};
    img.src=u;
  });
}
async function handleImageFile(file){
  if(!file) return;
  if(!/^image\//.test(file.type)){toast('กรุณาเลือกไฟล์รูปภาพ',true);return;}
  if(file.size>25*1024*1024){toast('ไฟล์ใหญ่เกิน 25MB',true);return;}
  try{
    const dataUrl=await resizeImage(file);
    imgState.dataUrl=dataUrl;
    showImgPreview(dataUrl);
    $('imgNote').textContent='รูปจะอัปโหลดเมื่อกดบันทึก';
  }catch(e){toast(e.message,true);}
}
(function initImagePicker(){
  const pick=$('imgPick'),input=$('fImg');
  pick.addEventListener('click',()=>{if(!pick.classList.contains('busy')) input.click();});
  pick.addEventListener('keydown',e=>{if(e.key==='Enter'||e.key===' '){e.preventDefault();input.click();}});
  input.addEventListener('change',()=>handleImageFile(input.files[0]));
  ['dragenter','dragover'].forEach(ev=>pick.addEventListener(ev,e=>{e.preventDefault();pick.classList.add('drag');}));
  ['dragleave','drop'].forEach(ev=>pick.addEventListener(ev,e=>{e.preventDefault();pick.classList.remove('drag');}));
  pick.addEventListener('drop',e=>handleImageFile(e.dataTransfer.files[0]));
  // วางรูปจากคลิปบอร์ด (Ctrl+V) ขณะเปิดฟอร์ม
  document.addEventListener('paste',e=>{
    if(!$('fmOv').classList.contains('on')) return;
    const f=[...(e.clipboardData?.files||[])].find(x=>/^image\//.test(x.type));
    if(f){e.preventDefault();handleImageFile(f);}
  });
})();

/* ========== ลบ (ต้องระบุเหตุผล ตามที่ server บังคับ) ========== */
let delState=null;

async function openDelete(id){
  if(!CAN_EDIT) return;
  const f=findItemById(id);
  if(!f){toast('ไม่พบสินค้านี้ อาจถูกลบไปแล้ว',true);return;}
  delState={id};
  $('delSub').textContent=`${id} · ${f.item.name||''}`;
  $('delReason').value='';
  $('delBtn').disabled=false;
  $('delWarn').textContent='กำลังตรวจสอบ Transaction...';
  $('delOv').classList.add('on');
  setTimeout(()=>$('delReason').focus(),50);

  const childCount=(allSubs[id]||[]).length;
  let msg='';
  try{
    const c=(await API.get('/api/items/'+encodeURIComponent(id)+'/tx-count')).count||0;
    msg=c>0?`⚠️ Transaction ${c} รายการของสินค้านี้จะถูกลบไปด้วย และกู้คืนไม่ได้`:'สินค้านี้ยังไม่มี Transaction';
  }catch(e){
    msg='ตรวจสอบจำนวน Transaction ไม่ได้ — Transaction ของสินค้านี้ (ถ้ามี) จะถูกลบไปด้วย';
  }
  if(childCount>0) msg+=`\nรายการย่อย ${childCount} รายการจะไม่ถูกลบตาม`;
  if(delState?.id===id) $('delWarn').textContent=msg;
}

function closeDelete(skipFlush){
  $('delOv').classList.remove('on');
  delState=null;
  if(!skipFlush) flushItemsPendingRefresh();
}

async function confirmDelete(){
  if(!delState) return;
  const reason=$('delReason').value.trim();
  if(!reason){toast('กรุณาระบุเหตุผลในการลบ',true);$('delReason').focus();return;}
  const btn=$('delBtn');
  btn.disabled=true;btn.textContent='กำลังลบ...';
  try{
    await API.del('/api/items/'+encodeURIComponent(delState.id)+'?reason='+encodeURIComponent(reason));
    toast('ลบเรียบร้อย');
    closeDelete(true);
    itemsRefreshPending=false;
    await fetchAllAndRender(false,true);
  }catch(e){
    toast(e.message||'ลบไม่สำเร็จ',true);
  }finally{
    btn.disabled=false;btn.textContent='ลบ';
  }
}

document.addEventListener('keydown',e=>{
  if(e.key!=='Escape') return;
  if($('delOv').classList.contains('on')) closeDelete();
  else if($('fmOv').classList.contains('on')) closeItemForm();
  else if($('txOv').classList.contains('on')) closeTx();
});

/* ========== Auto-refresh (SSE) ========== */
let itemsRefreshPending=false;
let itemsRefreshDebounce=null;

function isBusyEditingItems(){
  return $('txOv').classList.contains('on')||$('fmOv').classList.contains('on')||$('delOv').classList.contains('on');
}

function requestItemsSilentRefresh(){
  clearTimeout(itemsRefreshDebounce);
  itemsRefreshDebounce=setTimeout(()=>{
    if(isBusyEditingItems()){itemsRefreshPending=true;return;}
    fetchAllAndRender(false,true);
  },300);
}

function flushItemsPendingRefresh(){
  if(!itemsRefreshPending||isBusyEditingItems()) return;
  itemsRefreshPending=false;
  fetchAllAndRender(false,true);
}

function connectItemsSSE(){
  if(!NEST_URL) return; // หน้านี้ไม่ได้ login เลยไม่มีค่า nest service ตั้งใจข้ามการเชื่อมต่อ real-time
  const url=`${NEST_URL}/items/events?key=${encodeURIComponent(NEST_KEY)}`;
  const es=new EventSource(url);
  es.onmessage=(e)=>{
    if(e.data==='heartbeat') return;
    if(e.data==='items') requestItemsSilentRefresh();
  };
  es.onerror=()=>console.warn('[SSE items] connection issue, browser will auto-retry');
}

fetchAllAndRender(true);
connectItemsSSE();
</script>
</body>
</html>