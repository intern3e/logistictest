<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>รายการสินค้า เข้า-ออก - 3E TRADING</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <meta name="theme-color" content="#FFFFFF" id="metaTheme">
  <script>(function(){var t="light";try{t=localStorage.getItem("3e-theme")||"light"}catch(e){}document.documentElement.dataset.theme=t;})();</script>
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
  <style>
    /* =====================================================================
       3E TRADING · รายการสินค้า เข้า-ออก
       ธีมมืดเป็นค่าเริ่มต้น (สลับเป็นสว่างได้ด้วยปุ่มบนแถบบน)
       สีเน้น: น้ำเงิน #1A4FD6 · ฟอนต์: Sarabun (ตัวอักษรไทยแบบมีหัว)
       ===================================================================== */
    :root{
      --bg:#FFFFFF; --surface:#FFFFFF; --surface-2:#F4F5F3; --surface-3:#E9EAE7;
      --line:#DCDDD8; --line-soft:#E9EAE6;
      --ink:#1D2023; --ink-2:#4A4E52; --slate:#6E7276; --mute:#9A9D9F;
      --accent:#1A4FD6; --accent-ring:rgba(26,79,214,.22); --glow:rgba(26,79,214,.55);
      --alert:#B42318; --media:#E4E5E1; --ph-a:#ECEDE9; --ph-b:#E1E2DE; --ph-ink:#A9ACA8;
      --hdr-bg:rgba(255,255,255,.86); --ov-bg:rgba(238,239,236,.86);
      --btn:#1D2023; --btn-hover:#000; --btn-ink:#FFFFFF;
      --card-ring:rgba(29,32,35,.07);
      --card-shadow:0 1px 2px rgba(29,32,35,.05),0 12px 32px -18px rgba(29,32,35,.22);
      --shadow-lift:0 1px 2px rgba(29,32,35,.06),0 26px 50px -22px rgba(29,32,35,.34);
      --cap-a:rgba(17,19,21,.74); --cap-b:rgba(17,19,21,.30);
      --ease:cubic-bezier(.2,.7,.2,1);
      --r-card:16px;
      color-scheme:light;
    }
    :root[data-theme="dark"]{
      --bg:#111214; --surface:#191B1E; --surface-2:#212327; --surface-3:#2A2D31;
      --line:rgba(255,255,255,.10); --line-soft:rgba(255,255,255,.07);
      --ink:#F3F2EF; --ink-2:#C8C9CA; --slate:#989CA0; --mute:#6C7075;
      --accent:#5B8CFF; --accent-ring:rgba(91,140,255,.26); --glow:rgba(91,140,255,.75);
      --alert:#FF7468; --media:#1F2124; --ph-a:#202226; --ph-b:#18191C; --ph-ink:#4F5358;
      --hdr-bg:rgba(17,18,20,.68); --ov-bg:rgba(13,14,16,.82);
      --btn:#F3F2EF; --btn-hover:#FFFFFF; --btn-ink:#111214;
      --card-ring:rgba(255,255,255,.06);
      --card-shadow:none;
      --shadow-lift:0 0 0 1px rgba(255,255,255,.05),0 34px 70px -28px rgba(0,0,0,.85);
      --cap-a:rgba(8,9,10,.86); --cap-b:rgba(8,9,10,.34);
      color-scheme:dark;
    }
    *{margin:0;padding:0;box-sizing:border-box}
    html{-webkit-text-size-adjust:100%;background:var(--bg)}
    body{position:relative;min-height:100vh;font-family:'Sarabun','Tahoma',system-ui,sans-serif;font-size:14px;line-height:1.5;color:var(--ink);background:transparent;-webkit-font-smoothing:antialiased;font-feature-settings:"tnum" 1}
    button,input{font:inherit;color:inherit}
    :focus-visible{outline:2px solid var(--accent);outline-offset:2px}
    ::selection{background:var(--accent-ring)}

    /* แสงฟุ้ง + เกรนฟิล์ม (เฉพาะธีมมืด) */
    :root[data-theme="dark"] body::before{content:'';position:fixed;inset:0;z-index:-1;pointer-events:none;
      background:
        radial-gradient(1000px 600px at 4% -6%,rgba(91,140,255,.22),transparent 62%),
        radial-gradient(820px 620px at 102% 2%,rgba(96,128,255,.14),transparent 60%),
        radial-gradient(1100px 700px at 50% 120%,rgba(91,140,255,.06),transparent 60%)}
    :root[data-theme="dark"] body::after{content:'';position:fixed;inset:0;z-index:-1;pointer-events:none;opacity:.07;mix-blend-mode:overlay;
      background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E")}

    /* สลับธีมแบบวงกลมขยายจากปุ่ม (View Transitions) */
    ::view-transition-old(root),::view-transition-new(root){animation:none;mix-blend-mode:normal}

    /* ===================== ตัวโหลดเต็มจอ ===================== */
    .ov{position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:var(--ov-bg);backdrop-filter:blur(16px) saturate(140%);-webkit-backdrop-filter:blur(16px) saturate(140%);opacity:0;visibility:hidden;transition:opacity .4s var(--ease),visibility .4s}
    .ov.on{opacity:1;visibility:visible}
    .progress-container{width:260px;text-align:center}
    .ov-mark{position:relative;width:76px;height:76px;margin:0 auto 22px;display:grid;place-items:center}
    .ov-mark::before{content:'';position:absolute;inset:0;border-radius:50%;background:conic-gradient(from 0deg,transparent 0 62%,var(--accent) 100%);-webkit-mask:radial-gradient(farthest-side,transparent calc(100% - 2px),#000 calc(100% - 2px));mask:radial-gradient(farthest-side,transparent calc(100% - 2px),#000 calc(100% - 2px));animation:sp 1.1s linear infinite}
    .ov-logo{width:42px;height:42px;object-fit:contain}
    .ov-text{font-size:15px;font-weight:500;color:var(--ink);margin-bottom:16px}
    .progress-track{height:2px;border-radius:2px;background:var(--line);overflow:hidden}
    .progress-fill{width:0%;height:100%;border-radius:2px;background:var(--accent);box-shadow:0 0 12px var(--accent);transition:width .2s var(--ease)}
    .ov-percent{margin-top:10px;font-size:12px;font-weight:500;color:var(--slate)}

    /* ===================== แถบบน ===================== */
    .hdr{position:sticky;top:0;z-index:2000;background:var(--hdr-bg);backdrop-filter:saturate(170%) blur(18px);-webkit-backdrop-filter:saturate(170%) blur(18px);border-bottom:1px solid var(--line-soft);transition:box-shadow .3s}
    .hdr.scrolled{box-shadow:0 14px 34px -24px rgba(0,0,0,.5)}
    .hdr-in{max-width:1880px;margin:0 auto;padding:0 32px;min-height:76px;display:flex;align-items:center;gap:8px}
    .nav-logo{flex-shrink:0;display:flex;align-items:center;gap:11px;margin-right:18px;color:var(--ink);text-decoration:none}
    .nav-logo img{height:36px;width:36px;object-fit:contain;transition:transform .8s var(--ease)}
    .nav-logo:hover img{transform:rotate(90deg)}
    .nav-logo span{display:flex;flex-direction:column;line-height:1.1}
    .nav-logo b{font-size:15px;font-weight:700;letter-spacing:.03em}
    .nav-logo small{margin-top:3px;font-size:11.5px;font-weight:500;color:var(--slate)}
    .nav-links{flex-shrink:0;display:flex;align-items:center;gap:4px}
    .nav-links a{position:relative;padding:10px 12px;border-radius:8px;font-size:14px;font-weight:500;color:var(--slate);text-decoration:none;white-space:nowrap;transition:color .2s}
    .nav-links a:hover{color:var(--ink)}
    .nav-links a.on{color:var(--ink);font-weight:600}
    .nav-links a.on::after{content:'';position:absolute;left:12px;right:12px;bottom:2px;height:2px;border-radius:2px;background:var(--accent);box-shadow:0 0 10px var(--accent)}

    /* ช่องค้นหา: ชื่อสินค้า | ยี่ห้อ */
    .sbar{flex:0 1 560px;min-width:0;margin-left:14px;display:flex;align-items:center;height:46px;padding:0 6px 0 4px;border:1px solid var(--line-soft);border-radius:999px;background:var(--surface-2);transition:background .2s,border-color .2s,box-shadow .25s}
    .sbar:hover{border-color:var(--line)}
    .sbar:focus-within{background:var(--surface);border-color:var(--accent);box-shadow:0 0 0 4px var(--accent-ring),0 10px 30px -12px var(--accent-ring)}
    .seg{position:relative;flex:1;min-width:0;height:100%;display:flex;align-items:center;gap:9px;padding:0 14px;color:var(--mute);cursor:text}
    .seg:focus-within{color:var(--ink)}
    .seg svg{flex-shrink:0}
    .seg-name{flex:1.7}
    .seg+.seg::before{content:'';position:absolute;left:0;top:13px;bottom:13px;width:1px;background:var(--line)}
    .seg input{flex:1;min-width:0;height:100%;border:none;outline:none;background:transparent;font-size:14px;color:var(--ink)}
    .seg input::placeholder{color:var(--slate)}
    .seg input::-webkit-search-cancel-button{display:none}
    .seg kbd{font-family:inherit;font-size:11px;font-weight:600;line-height:18px;padding:0 7px;color:var(--slate);background:var(--surface);border:1px solid var(--line);border-radius:6px}
    .seg:focus-within kbd{display:none}
    .seg-brand .chev{color:var(--mute);transition:transform .25s var(--ease),color .2s}
    .seg-brand.open .chev{transform:rotate(180deg);color:var(--ink)}
    .bdd{position:absolute;top:calc(100% + 12px);left:0;z-index:2100;min-width:260px;width:calc(100% + 40px);max-height:360px;overflow-y:auto;padding:6px;cursor:default;background:var(--surface);border:1px solid var(--line-soft);border-radius:14px;box-shadow:var(--shadow-lift);animation:pop .22s var(--ease)}
    @keyframes pop{from{opacity:0;transform:translateY(-6px) scale(.98)}}
    .bdd[hidden]{display:none}
    .bdd-opt{display:flex;align-items:center;width:100%;padding:9px 12px;border:none;border-radius:9px;background:none;font-size:14px;color:var(--ink);text-align:left;cursor:pointer}
    .bdd-opt:hover,.bdd-opt.active{background:var(--surface-2)}
    .bdd-opt.on{font-weight:600}
    .bdd-opt.on::after{content:'';margin-left:auto;width:8px;height:8px;border-radius:50%;background:var(--accent);box-shadow:0 0 8px var(--accent)}
    .bdd-all{color:var(--slate)}
    .bdd-sep{height:1px;margin:4px 8px;background:var(--line-soft)}
    .bdd-empty{padding:12px;font-size:13px;color:var(--mute)}
    .bdd mark{background:none;color:inherit;font-weight:700;text-decoration:underline;text-decoration-color:var(--accent);text-underline-offset:3px}
    .mini-load{display:none;flex-shrink:0;width:16px;height:16px;margin-right:8px;border:2px solid var(--line);border-top-color:var(--accent);border-radius:50%;animation:sp .8s linear infinite}
    .mini-load.on{display:block}
    .sbar-x{flex-shrink:0;height:32px;padding:0 13px;border:none;border-radius:999px;background:var(--btn);color:var(--btn-ink);font-size:12.5px;font-weight:600;white-space:nowrap;cursor:pointer;transition:background .2s}
    .sbar-x:hover{background:var(--btn-hover)}
    .sbar-x[hidden]{display:none}

    .nav-right{flex-shrink:0;display:flex;align-items:center;gap:8px;margin-left:auto}
    .theme-btn{position:relative;flex-shrink:0;width:42px;height:42px;border:1px solid var(--line-soft);border-radius:999px;background:var(--surface-2);color:var(--ink);cursor:pointer;display:grid;place-items:center;overflow:hidden;transition:background .2s,border-color .2s}
    .theme-btn:hover{border-color:var(--line)}
    .theme-btn svg{grid-area:1/1;transition:transform .5s var(--ease),opacity .3s}
    .theme-btn .i-sun{opacity:0;transform:rotate(-90deg) scale(.6)}
    :root[data-theme="dark"] .theme-btn .i-sun{opacity:1;transform:none;color:var(--accent)}
    :root[data-theme="dark"] .theme-btn .i-moon{opacity:0;transform:rotate(90deg) scale(.6)}
    .nav-user{display:flex;align-items:center;gap:9px;height:42px;padding:0 15px 0 4px;border:1px solid var(--line);border-radius:999px;background:var(--surface);font-size:14px;font-weight:500;white-space:nowrap;cursor:default}
    .nav-user-ic{width:32px;height:32px;flex-shrink:0;border-radius:50%;background:var(--surface-2);color:var(--ink-2);display:flex;align-items:center;justify-content:center}
    .nav-user-name{max-width:160px;overflow:hidden;text-overflow:ellipsis}
    .nav-btn{display:inline-flex;align-items:center;height:42px;padding:0 18px;border-radius:999px;background:var(--btn);color:var(--btn-ink);font-size:14px;font-weight:600;text-decoration:none;white-space:nowrap;transition:background .2s,transform .2s var(--ease)}
    .nav-btn:hover{background:var(--btn-hover);transform:translateY(-1px)}
    .hamburger{display:none;flex-shrink:0;width:42px;height:42px;border:1px solid var(--line-soft);border-radius:999px;background:var(--surface-2);cursor:pointer;flex-direction:column;align-items:center;justify-content:center;gap:4px}
    .hamburger span{display:block;width:16px;height:1.75px;border-radius:2px;background:var(--ink)}

    /* ===================== เมนูด้านข้าง (จอแคบ) ===================== */
    .sb-ov{position:fixed;inset:0;z-index:2500;background:rgba(0,0,0,.45);opacity:0;pointer-events:none;transition:opacity .25s}
    .sb-ov.open{opacity:1;pointer-events:all}
    .sidebar{position:fixed;top:0;left:0;z-index:2600;width:290px;height:100vh;display:flex;flex-direction:column;background:var(--surface);border-right:1px solid var(--line-soft);transform:translateX(-105%);transition:transform .35s var(--ease),box-shadow .35s}
    .sidebar.open{transform:none;box-shadow:24px 0 60px -24px rgba(0,0,0,.6)}
    .sb-head{display:flex;align-items:center;gap:10px;padding:18px 20px;border-bottom:1px solid var(--line-soft)}
    .sb-head img{height:32px;width:32px;object-fit:contain}
    .sb-head span{flex:1;font-size:15px;font-weight:700;letter-spacing:.03em}
    .sb-close{width:34px;height:34px;border:none;border-radius:999px;background:var(--surface-2);cursor:pointer;font-size:13px}
    .sb-nav{flex:1;overflow-y:auto;padding:14px 12px}
    .sb-sec{padding:6px 10px 8px;font-size:12px;font-weight:600;color:var(--mute)}
    .sb-item{display:flex;align-items:center;gap:12px;padding:11px 12px;border-radius:10px;color:var(--ink-2);font-size:14px;font-weight:500;text-decoration:none;transition:background .2s,color .2s}
    .sb-item:hover{background:var(--surface-2);color:var(--ink)}
    .sb-item.cur{background:var(--surface-2);color:var(--ink);font-weight:600}
    .sb-item svg{color:var(--slate)}

    /* ===================== เนื้อหา ===================== */
    #content{max-width:1880px;margin:0 auto;padding:36px 32px 64px}

    /* ===================== ประวัติเข้า-ออก ===================== */
    .tx-ov{position:fixed;inset:0;z-index:5000;display:none;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,.5);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px)}
    .tx-ov.on{display:flex;animation:fadeIn .25s var(--ease)}
    .tx-ov.on .tx-modal{animation:riseM .4s var(--ease)}
    @keyframes fadeIn{from{opacity:0}}
    @keyframes riseM{from{opacity:0;transform:translateY(16px) scale(.985)}}
    @media(prefers-reduced-motion:reduce){.tx-ov.on,.tx-ov.on .tx-modal,.bdd{animation:none}}
    .tx-modal{width:min(1400px,96vw);height:88vh;display:flex;flex-direction:column;overflow:hidden;background:var(--surface);border:1px solid var(--line-soft);border-radius:20px;box-shadow:0 50px 100px -30px rgba(0,0,0,.7)}
    .tx-head{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:20px 24px;border-bottom:1px solid var(--line-soft)}
    .tx-head-t{display:flex;align-items:baseline;flex-wrap:wrap;gap:6px 12px;min-width:0}
    .tx-head h3{font-size:18px;font-weight:600}
    .tx-badge{padding:2px 10px;border-radius:999px;background:var(--surface-2);font-size:13px;font-weight:600}
    #txName{font-size:14px;color:var(--slate)}
    .tx-xbtn{flex-shrink:0;width:40px;height:40px;border:none;border-radius:999px;background:var(--surface-2);color:var(--ink);cursor:pointer;display:grid;place-items:center;transition:background .2s}
    .tx-xbtn:hover{background:var(--surface-3)}
    .tx-body{flex:1;overflow:auto}
    .tx-foot{padding:14px 24px;border-top:1px solid var(--line-soft);text-align:right;font-size:13px;color:var(--slate)}
    .tx-spin{display:flex;justify-content:center;padding:64px}
    .tx-spin-inner{width:28px;height:28px;border:2px solid var(--line);border-top-color:var(--accent);border-radius:50%;animation:sp .8s linear infinite}
    @keyframes sp{to{transform:rotate(360deg)}}
    .tx-tbl{width:100%;border-collapse:separate;border-spacing:0;font-size:13.5px}
    .tx-tbl th{position:sticky;top:0;z-index:1;padding:12px 18px;background:var(--surface-2);border-bottom:1px solid var(--line-soft);text-align:left;font-size:12.5px;font-weight:500;color:var(--slate);white-space:nowrap}
    .tx-tbl td{padding:13px 18px;border-bottom:1px solid var(--line-soft);vertical-align:middle}
    .tx-tbl tbody tr:hover td{background:var(--surface-2)}
    .tx-tbl td.num,.tx-tbl th.num{text-align:right}
    .tx-type{display:inline-flex;align-items:center;gap:8px;font-weight:500;white-space:nowrap}
    .tx-type::before{content:'';width:8px;height:8px;border-radius:50%;background:var(--mute)}
    .t-in::before{background:#2BB673;box-shadow:0 0 8px #2BB67380}.t-ret::before{background:#29A7C9}.t-sell::before{background:#E5483D;box-shadow:0 0 8px #E5483D80}.t-bor::before{background:#E2AA25}.t-wit::before{background:#E37F0A}
    .tx-tbl a{color:var(--ink);font-weight:600;text-decoration:underline;text-decoration-color:var(--accent);text-underline-offset:3px}
    .tx-empty{padding:72px 16px;text-align:center;color:var(--slate);font-size:15px}

    .toast{position:fixed;left:50%;bottom:28px;z-index:9999;padding:12px 20px;border-radius:999px;background:var(--btn);color:var(--btn-ink);font-size:14px;font-weight:500;box-shadow:0 18px 40px -14px rgba(0,0,0,.6);opacity:0;transform:translate(-50%,10px);transition:opacity .3s,transform .3s var(--ease);pointer-events:none}
    .toast.on{opacity:1;transform:translate(-50%,0)}
    .toast.err{background:#B42318;color:#fff}

    /* ===================== จอแคบ / มือถือ ===================== */
    @media(max-width:1240px){
      .nav-links{display:none}
      .hamburger{display:flex}
      .nav-logo{margin-right:6px}
    }
    @media(max-width:760px){
      .hdr-in{flex-wrap:wrap;padding:10px 16px 12px;row-gap:10px;min-height:0}
      .nav-logo small{display:none}
      .nav-btn{display:none}
      .nav-user{padding:0 4px}
      .nav-user-name{display:none}
      .sbar{order:3;flex:1 1 100%;margin-left:0}
      .seg{padding:0 12px}
      .seg kbd{display:none}
      .bdd{left:auto;right:-6px;width:auto;min-width:240px}
      #content{padding:22px 16px 44px}
      .v-grid{grid-template-columns:1fr;gap:20px}
      .pg-btn span{display:none}
      .pg-btn{padding:0 14px}
      .tx-modal{height:92vh;border-radius:16px}
      .tx-head{padding:16px}
    }

    /* ===================== หน้า รายการสินค้า เข้า-ออก ===================== */
    #content{max-width:1880px;margin:0 auto;padding:24px 32px 64px}
    .page-head{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:18px}
    .page-head h1{font-size:26px;font-weight:600;letter-spacing:-.01em;line-height:1.2}
    .page-head p{margin-top:4px;font-size:14px;color:var(--slate)}
    .page-head p b{color:var(--ink);font-weight:600}
    .page-acts{display:flex;align-items:center;gap:8px}
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;height:42px;padding:0 18px;border:none;border-radius:999px;font-size:14px;font-weight:600;white-space:nowrap;cursor:pointer;transition:background .2s,border-color .2s,transform .2s var(--ease),box-shadow .2s}
    .btn-ghost{background:var(--surface);color:var(--ink);border:1px solid var(--line)}
    .btn-ghost:hover{border-color:var(--ink)}
    .btn-solid{background:var(--btn);color:var(--btn-ink)}
    .btn-solid:hover{background:var(--btn-hover);transform:translateY(-1px);box-shadow:0 10px 24px -12px rgba(0,0,0,.4)}
    .btn:disabled{opacity:.5;cursor:not-allowed;transform:none;box-shadow:none}
    .btn-ghost[hidden]{display:none}

    /* หัวข้อหน้า + จำนวนรายการ บนแถบบน */
    .nav-title{flex-shrink:0;display:flex;flex-direction:column;justify-content:center;line-height:1.25;margin:0 14px 0 2px;padding:0 18px;border-left:1px solid var(--line);border-right:1px solid var(--line)}
    .nav-title b{font-size:14.5px;font-weight:600;white-space:nowrap}
    .nav-title span{font-size:12.5px;color:var(--slate);white-space:nowrap}
    .nav-title span b{font-size:12.5px;color:var(--ink)}
    /* ปุ่มท้ายแผงตัวกรอง */
    .f-acts{display:flex;align-items:center;justify-content:flex-end;gap:8px;padding:8px 12px}
    .f-acts .btn{height:38px;padding:0 15px;font-size:13.5px}
    /* แผงตัวกรอง */
    .filters{display:grid;grid-template-columns:150px 150px 1fr 1fr 130px 1.4fr 110px auto;gap:0;margin:0 0 22px;background:var(--surface);border:1px solid var(--line-soft);border-radius:16px;box-shadow:0 0 0 1px var(--card-ring),var(--card-shadow,none);position:relative;z-index:20}
    .fld{position:relative;display:flex;flex-direction:column;gap:2px;padding:10px 16px 9px;min-width:0;transition:background .2s}
    .fld+.fld::before{content:'';position:absolute;left:0;top:12px;bottom:12px;width:1px;background:var(--line-soft)}
    .fld:focus-within::after{content:'';position:absolute;left:16px;right:16px;bottom:0;height:2px;border-radius:2px;background:var(--accent)}
    .fld label{font-size:12px;font-weight:500;color:var(--slate)}
    .fld input{width:100%;height:26px;border:none;outline:none;background:transparent;font-size:14px;color:var(--ink)}
    .fld input::placeholder{color:var(--mute)}
    .fld input::-webkit-search-cancel-button{display:none}
    .fld input[type=date]{color-scheme:inherit}
    .fld select{width:100%;height:26px;padding-right:18px;border:none;outline:none;background:transparent;font-size:14px;color:var(--ink);cursor:pointer;appearance:none;-webkit-appearance:none}
    .fld select option{color:#1D2023}
    .fld-sel>i{position:absolute;right:18px;bottom:19px;width:6px;height:6px;border-right:1.6px solid var(--slate);border-bottom:1.6px solid var(--slate);transform:rotate(45deg);pointer-events:none}
    .fld input[type=date]:invalid{color:var(--mute)}

    /* ตาราง: มีเส้นตาราง + สีสลับแถว + แถบสีตามประเภท ให้อ่านง่าย */
    :root{--grid:#E3E5E1;--th-bg:#F1F3F6;--th-ink:#2B3036;--th-line:#CDD2D9;--zebra:#FAFAF8;--row-hover:#EEF3FF;--day-bg:#EEF3FF;--day-ink:#12379A}
    :root[data-theme="dark"]{--grid:rgba(255,255,255,.08);--th-bg:#24272B;--th-ink:#E9EAEB;--th-line:rgba(255,255,255,.16);--zebra:rgba(255,255,255,.025);--row-hover:rgba(91,140,255,.08);--day-bg:rgba(91,140,255,.10);--day-ink:#A9C1FF}
    .tbl-wrap{background:var(--surface);border:1px solid var(--th-line);border-radius:14px;overflow-x:auto;box-shadow:0 0 0 1px var(--card-ring),var(--card-shadow,none)}
    table{width:100%;border-collapse:separate;border-spacing:0;font-size:14px}
    th{padding:13px 16px;background:var(--th-bg);border-bottom:2px solid var(--th-line);border-right:1px solid var(--grid);text-align:left;font-size:13px;font-weight:600;color:var(--th-ink);white-space:nowrap}
    th:last-child,td:last-child{border-right:none}
    #content table th,#content table th.num{text-align:center}
    th.num,td.num{text-align:right}
    td{padding:12px 16px;border-bottom:1px solid var(--grid);border-right:1px solid var(--grid);vertical-align:middle;color:var(--ink)}
    tbody tr.row td{transition:background .15s}
    tbody tr.row.alt td{background:var(--zebra)}
    tbody tr.row:hover td{background:var(--row-hover)}
    /* ช่องประเภท: สีเต็มช่อง */
    td.tcell,tbody tr.row.alt td.tcell,tbody tr.row:hover td.tcell{background:var(--tfill);color:#fff;text-align:center;font-weight:600;font-size:13px;white-space:nowrap}
    td.tcell.t-in{--tfill:#1E9E62}
    td.tcell.t-ret{--tfill:#1F9BBC}
    td.tcell.t-sell{--tfill:#D8392E}
    td.tcell.t-bor{--tfill:#D49A12}
    td.tcell.t-wit{--tfill:#E37F0A}
    :root[data-theme="dark"] td.tcell.t-in{--tfill:#178553}
    :root[data-theme="dark"] td.tcell.t-ret{--tfill:#1A83A0}
    :root[data-theme="dark"] td.tcell.t-sell{--tfill:#B9322A}
    :root[data-theme="dark"] td.tcell.t-bor{--tfill:#B5840F}
    :root[data-theme="dark"] td.tcell.t-wit{--tfill:#C46C08}
    tbody tr:last-child td{border-bottom:none}
    tr.day td{padding:10px 16px;background:var(--day-bg);border-bottom:1px solid var(--grid);border-right:none;box-shadow:inset 4px 0 0 var(--accent)}
    tr.day:first-child td{padding-top:10px}
    .day-l{display:flex;align-items:baseline;gap:10px}
    .day-l b{font-size:14px;font-weight:700;color:var(--day-ink)}
    .day-l span{font-size:12.5px;color:var(--slate)}
    .t-time{font-size:13.5px;color:var(--ink-2);white-space:nowrap}
    .t-op{white-space:nowrap}
    .t-bill{font-weight:500;white-space:nowrap}
    td.t-bill,td.t-shelf,td.t-qty{text-align:center} /* เอกสาร และ ชั้นวาง: ข้อมูลอยู่กึ่งกลางช่อง */
    .t-item{min-width:220px;max-width:420px;font-weight:500}
    .t-note{min-width:140px;max-width:280px;color:var(--ink-2);font-size:13px}
    .muted{color:var(--mute)}
    .qty{font-weight:600;white-space:nowrap}
    .qty.in{color:#13804C}
    .qty.out{color:#C2332A}
    :root[data-theme="dark"] .qty.in{color:#5BD69A}
    :root[data-theme="dark"] .qty.out{color:#FF8378}

    /* ป้ายประเภท */
    .ttag{display:inline-flex;align-items:center;gap:7px;height:28px;padding:0 11px;border-radius:999px;font-size:12.5px;font-weight:600;white-space:nowrap;background:var(--tb);color:var(--tc)}
    .ttag::before{content:'';width:7px;height:7px;border-radius:50%;background:var(--td)}
    .t-in{--tb:#E4F4EC;--tc:#13704A;--td:#1E9E62}
    .t-ret{--tb:#E2F3F8;--tc:#16697F;--td:#1F9BBC}
    .t-sell{--tb:#FBE7E5;--tc:#A12A22;--td:#D8392E}
    .t-bor{--tb:#FBF1DA;--tc:#7E5806;--td:#D49A12}
    .t-wit{--tb:#FDEBDD;--tc:#99490A;--td:#E37F0A}
    :root[data-theme="dark"] .t-in{--tb:rgba(43,182,115,.14);--tc:#7FDDB3;--td:#2BB673}
    :root[data-theme="dark"] .t-ret{--tb:rgba(41,167,201,.14);--tc:#8AD3E8;--td:#29A7C9}
    :root[data-theme="dark"] .t-sell{--tb:rgba(229,72,61,.15);--tc:#FF9F94;--td:#E5483D}
    :root[data-theme="dark"] .t-bor{--tb:rgba(226,170,37,.14);--tc:#F6CF72;--td:#E2AA25}
    :root[data-theme="dark"] .t-wit{--tb:rgba(242,154,46,.15);--tc:#F8BD72;--td:#F29A2E}

    /* รูปย่อ */
    .thumb{position:relative;display:block;width:44px;height:44px;padding:0;border:none;border-radius:10px;overflow:hidden;cursor:zoom-in;background:var(--surface-2);box-shadow:0 0 0 1px var(--line-soft)}
    .thumb img{width:100%;height:100%;object-fit:cover;display:block;opacity:0;transition:opacity .4s,transform .4s var(--ease)}
    .thumb img.ready{opacity:1}
    .thumb:hover img{transform:scale(1.08)}
    .thumb.fail{width:auto;height:30px;padding:0 12px;font-size:12.5px;font-weight:600;color:var(--ink);cursor:pointer}

    /* ปุ่มในตาราง */
    .acts{white-space:nowrap}
    tr.editing td{padding:10px 8px}
    tr.editing td:first-child{padding-left:16px}
    tr.editing .acts{white-space:normal;min-width:140px}
    tr.editing .acts .ib{margin:3px 4px 3px 0}
    tr.editing .acts .ib+.ib{margin-left:0}
    .acts .ib{height:32px;padding:0 13px;border:1px solid var(--line);border-radius:999px;background:var(--surface);color:var(--ink);font-size:12.5px;font-weight:600;cursor:pointer;transition:border-color .2s,background .2s,color .2s}
    .acts .ib+.ib{margin-left:6px}
    .acts .ib:hover{border-color:var(--ink)}
    .acts .a-save{background:var(--btn);border-color:var(--btn);color:var(--btn-ink)}
    .acts .a-del{color:var(--alert)}
    .acts .a-del:hover{border-color:var(--alert)}
    tr.editing td{background:var(--surface-2)}
    .edit-input{width:100%;min-width:64px;height:34px;padding:0 10px;border:1px solid var(--line);border-radius:9px;background:var(--surface);color:var(--ink);font-size:13px;transition:border-color .2s,box-shadow .2s}
    .edit-input:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-ring)}

    .empty-row td{padding:72px 16px;text-align:center;border-bottom:none}
    .empty-row b{display:block;font-size:17px;font-weight:600}
    .empty-row span{display:block;margin-top:6px;color:var(--slate)}

    /* เลขหน้า */
    #paging{display:flex;justify-content:center;align-items:center;gap:10px;padding-top:32px}
    .pg-btn{display:inline-flex;align-items:center;gap:6px;height:44px;padding:0 20px;border:1px solid var(--line);border-radius:999px;background:var(--surface);color:var(--ink);font-size:14px;font-weight:600;cursor:pointer;transition:border-color .2s,box-shadow .2s}
    .pg-btn:hover{border-color:var(--accent);box-shadow:0 0 0 4px var(--accent-ring)}
    .pg-info{padding:0 14px;font-size:14px;color:var(--slate)}
    .pg-info b{color:var(--ink);font-weight:600}

    /* หน้าต่างดูรูป */
    .modal-ov{position:fixed;inset:0;z-index:10000;display:flex;align-items:center;justify-content:center;padding:24px;background:rgba(8,9,10,.82);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);opacity:0;visibility:hidden;transition:opacity .3s var(--ease),visibility .3s}
    .modal-ov.on{opacity:1;visibility:visible}
    .modal-content{position:relative;display:flex;align-items:center;justify-content:center;min-width:280px;min-height:200px;max-width:min(1000px,94vw);max-height:88vh;transform:scale(.97);transition:transform .35s var(--ease)}
    .modal-ov.on .modal-content{transform:none}
    .modal-img{max-width:100%;max-height:82vh;object-fit:contain;display:block;border-radius:14px;box-shadow:0 40px 80px -30px rgba(0,0,0,.8)}
    .modal-img-loading{display:none;color:rgba(255,255,255,.8);font-size:14px}
    .modal-img-loading::before{content:'';display:block;width:28px;height:28px;margin:0 auto 12px;border:2px solid rgba(255,255,255,.2);border-top-color:#5B8CFF;border-radius:50%;animation:sp .8s linear infinite}
    .modal-img-error{display:none;padding:28px 32px;border-radius:16px;background:rgba(255,255,255,.06);color:#fff;text-align:center;line-height:1.6}
    .modal-img-error small{color:rgba(255,255,255,.65)}
    .modal-img-error a{display:inline-block;margin-top:10px;color:#A9C1FF;font-weight:600}
    .modal-close{position:fixed;top:20px;right:20px;width:44px;height:44px;border:none;border-radius:999px;background:rgba(255,255,255,.12);color:#fff;cursor:pointer;display:grid;place-items:center;transition:background .2s}
    .modal-close:hover{background:rgba(255,255,255,.22)}
    .modal-open-link{position:fixed;left:50%;bottom:24px;transform:translateX(-50%);display:inline-flex;align-items:center;gap:8px;height:42px;padding:0 18px;border-radius:999px;background:rgba(255,255,255,.12);color:#fff;font-size:13.5px;font-weight:600;text-decoration:none;transition:background .2s}
    .modal-open-link:hover{background:rgba(255,255,255,.22)}

    @media(max-width:1280px){
      .filters{grid-template-columns:repeat(3,minmax(0,1fr))}
      .f-acts{grid-column:1/-1;border-top:1px solid var(--line-soft)}
      .fld:nth-child(3n+1)::before{display:none}
      .fld:nth-child(n+4){border-top:1px solid var(--line-soft)}
    }
    @media(max-width:760px){
      #content{padding:22px 16px 44px}
      .nav-title{order:3;flex:1 1 100%;flex-direction:row;justify-content:flex-start;align-items:baseline;gap:10px;margin:0;padding:0;border:none}
      .f-acts .btn{flex:1}
      .filters{grid-template-columns:repeat(2,minmax(0,1fr))}
      .fld:nth-child(3n+1)::before{display:block}
      .fld:nth-child(2n+1)::before{display:none}
      .fld:nth-child(n+3){border-top:1px solid var(--line-soft)}
    }

    /* หน้านี้มีเมนูเยอะ: แสดงปุ่มเมนูทั้งหมดเสมอ (อยู่ซ้ายสุดหน้าโลโก้) */
    .hamburger{display:flex;margin-right:6px}

    /* ===================== ตัวเลือกแบบเด้งลง (แทน select เดิมของเบราว์เซอร์) ===================== */
    .fld-sel select{position:absolute;width:1px;height:1px;opacity:0;pointer-events:none}
    .dd-btn{display:flex;align-items:center;gap:8px;width:100%;height:26px;padding:0 20px 0 0;border:none;background:none;font-size:14px;color:var(--ink);text-align:left;cursor:pointer}
    .dd-btn:focus-visible{outline:none}
    .dd-val{display:flex;align-items:center;gap:7px;min-width:0;overflow:hidden;white-space:nowrap}
    .dd-val .dd-sub{overflow:hidden;text-overflow:ellipsis}
    .fld-sel>i{transition:transform .25s var(--ease)}
    .fld-sel.open>i{transform:translateY(3px) rotate(225deg)}
    .fld-sel.open::after,.fld-sel:has(.dd-btn:focus-visible)::after{content:'';position:absolute;left:16px;right:16px;bottom:0;height:2px;border-radius:2px;background:var(--accent)}
    .dd-pop{position:absolute;top:calc(100% + 6px);left:6px;z-index:2100;min-width:max(100%,230px);max-height:min(380px,60vh);overflow-y:auto;padding:6px;background:var(--surface);border:1px solid var(--line-soft);border-radius:14px;box-shadow:var(--shadow-lift);animation:ddPop .2s var(--ease)}
    .dd-pop.to-right{left:auto;right:6px}
    .dd-pop[hidden]{display:none}
    @keyframes ddPop{from{opacity:0;transform:translateY(-6px) scale(.98)}}
    @media(prefers-reduced-motion:reduce){.dd-pop{animation:none}}
    .dd-opt{display:flex;align-items:center;gap:9px;width:100%;min-height:40px;padding:7px 10px;border:none;border-radius:9px;background:none;font-size:14px;color:var(--ink);text-align:left;cursor:pointer;white-space:nowrap}
    .dd-opt:hover,.dd-opt:focus-visible{background:var(--surface-2);outline:none}
    .dd-opt.on{font-weight:600}
    .dd-opt.on::after{content:'';margin-left:auto;padding-left:10px;width:15px;height:15px;flex-shrink:0;background:var(--accent);-webkit-mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='20 6 9 17 4 12'/%3E%3C/svg%3E") right/15px no-repeat;mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='20 6 9 17 4 12'/%3E%3C/svg%3E") right/15px no-repeat}
    .dd-opt.dd-all{color:var(--slate)}
    .dd-opt.dd-all.on{color:var(--ink)}
    .dd-sep{height:1px;margin:4px 8px;background:var(--line-soft)}
    .dd-sub{color:var(--slate);font-weight:400}
    .dd-opt .dd-sub{font-size:13px}
    .dd-code{display:inline-grid;place-items:center;min-width:44px;height:24px;padding:0 8px;border-radius:7px;font-size:12px;font-weight:700;background:var(--bb,var(--surface-2));color:var(--bc,var(--ink-2))}
    .dd-btn .dd-code{min-width:0;height:22px;padding:0 7px;font-size:11.5px}
    .dd-btn .dd-ic{display:none} /* ไอคอน "ทั้งหมด" แสดงเฉพาะในรายการ */
    .dd-dot{width:9px;height:9px;flex-shrink:0;border-radius:50%;background:var(--td,var(--mute))}
    .dd-ic{display:grid;place-items:center;width:24px;height:24px;flex-shrink:0;border-radius:7px;background:var(--surface-2);color:var(--slate)}
    .dd-name{overflow:hidden;text-overflow:ellipsis}
    .dd-dot.k-klang{--td:#1E9E62}.dd-dot.k-asset{--td:#7A5AD6}
  </style>
</head>
<body>

<!-- ตัวโหลดเต็มจอ -->
<div class="ov" id="ov" role="status" aria-live="polite">
  <div class="progress-container">
    <div class="ov-mark"><img class="ov-logo" src="https://lh3.googleusercontent.com/d/1qruaZSyb6gXrJ1Bc_l-p50LdZ6mszbE0" alt=""></div>
    <p class="ov-text" id="ovText">กำลังโหลดข้อมูล...</p>
    <div class="progress-track"><div class="progress-fill" id="progressBar"></div></div>
    <p class="ov-percent" id="ovPercent">0%</p>
  </div>
</div>

<!-- หน้าต่างดูรูป (ใช้ img แทน iframe เพื่อเลี่ยง CSP) -->
<div class="modal-ov" id="modalOv" role="dialog" aria-modal="true" aria-label="รูปประกอบ">
  <button class="modal-close" onclick="closeModal()" aria-label="ปิด"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
  <div class="modal-content">
    <span class="modal-img-loading" id="modalLoading">กำลังโหลดรูปภาพ...</span>
    <img class="modal-img" id="modalImg" src="" alt="รูปประกอบ" style="display:none;">
    <div class="modal-img-error" id="modalError">
      โหลดรูปภาพไม่ได้<br>
      <small>รูปอาจยังไม่ได้ตั้งค่าแชร์ให้ทุกคนที่มีลิงก์ดูได้</small><br>
      <a id="modalFallbackLink" href="#" target="_blank" rel="noopener">เปิดใน Google Drive</a>
    </div>
  </div>
  <a class="modal-open-link" id="modalOpenLink" href="#" target="_blank" rel="noopener"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>เปิดใน Google Drive</a>
</div>

<div class="toast" id="toast" role="status" aria-live="polite"></div>

@php $q = ['create_by' => $authUser['name'] ?? '']; @endphp
<div class="sb-ov" id="sbOv" onclick="closeSB()"></div>
<div class="sidebar" id="sidebar">
 <div class="sb-head"><img src="https://lh3.googleusercontent.com/d/1qruaZSyb6gXrJ1Bc_l-p50LdZ6mszbE0" alt=""><span>3E TRADING</span><button class="sb-close" onclick="closeSB()" aria-label="ปิดเมนู">&#10005;</button></div>
  <div class="sb-nav">
    <div class="sb-sec">เมนู</div>
    <a class="sb-item cur" href="{{ route('inventory.transaction', $q) }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>รายการสินค้า เข้า-ออก</a>
    <a class="sb-item" target="_blank" href="{{ route('inventory.item', $q) }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>ค้นหาสินค้า</a>
    <a class="sb-item" target="_blank" href="{{ route('inventory.vehicles', $q) }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>รถบริษัท</a>
    @if(str_contains($authUser['page'] ?? '', 'pr'))
      <a class="sb-item" target="_blank" href="{{ route('inventory.pr', $q) }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>สร้างใบขอซื้อ</a>
    @endif
    @if(in_array($authRole, ['admin','user']))
      <div style="height:1px;background:var(--line-soft);margin:8px 10px"></div>
      <div class="sb-sec">ดำเนินการ</div>
      <a class="sb-item" target="_blank" href="{{ route('inventory.stockout', $q) }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>ขายสินค้าออก</a>
      <a class="sb-item" target="_blank" href="{{ route('inventory.withdraw', $q) }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>เบิกของ</a>
    @endif
      @if(in_array($authRole,['admin']))
      <a class="sb-item" target="_blank" href="{{ route('inventory.users', $q) }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>จัดการผู้ใช้งาน</a>
      <a class="sb-item" target="_blank" href="{{ route('inventory.pr.dashboard', $q) }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>ขอซื้อ</a>
      <div class="sb-sec">รายงาน</div>
      <a class="sb-item" target="_blank" href="{{ route('inventory.analyze') }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>วิเคราะห์สินค้า</a>
      <a class="sb-item" target="_blank" href="{{ route('inventory.edithistory') }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v5h5"/><path d="M3.05 13A9 9 0 1 0 6 5.3L3 8"/><path d="M12 7v5l4 2"/></svg>ประวัติการแก้ไข</a>
    @endif
  </div>
</div>


<header class="hdr" id="hdr">
  <div class="hdr-in">
    <button class="hamburger" onclick="openSB()" aria-label="เมนูทั้งหมด" title="เมนูทั้งหมด"><span></span><span></span><span></span></button>
    <a class="nav-logo" href="{{ route('inventory.transaction', $q) }}">
      <img src="https://lh3.googleusercontent.com/d/1qruaZSyb6gXrJ1Bc_l-p50LdZ6mszbE0" alt="">
      <span><b>3E TRADING</b><small>คลังสินค้าเครือ 3E</small></span>
    </a>
    <!-- หัวข้อหน้า + จำนวนรายการ (อยู่ก่อนเมนู) -->
    <div class="nav-title"><b>รายการสินค้า เข้า-ออก</b><span id="sumLine">กำลังโหลด...</span></div>
    <nav class="nav-links">
      <a target="_blank" href="{{ route('inventory.item', $q) }}">ค้นหาสินค้า</a>
      <a class="on" href="{{ route('inventory.transaction', $q) }}">รายการสินค้า เข้า-ออก</a>
      <a target="_blank" href="{{ route('inventory.vehicles', $q) }}">รถบริษัท</a>
    </nav>
    <div class="nav-right">
      <button type="button" class="theme-btn" id="themeBtn" onclick="toggleTheme(event)" aria-label="สลับโหมดสว่าง/มืด" title="สลับโหมดสว่าง/มืด"><svg class="i-sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="4.2"/><path d="M12 2.5v2.2M12 19.3v2.2M4.6 4.6l1.6 1.6M17.8 17.8l1.6 1.6M2.5 12h2.2M19.3 12h2.2M4.6 19.4l1.6-1.6M17.8 6.2l1.6-1.6"/></svg><svg class="i-moon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.5 14.5A8.5 8.5 0 0 1 9.5 3.5a8.5 8.5 0 1 0 11 11z"/></svg></button>
      <span class="nav-user" title="{{ $authUser['name'] ?? '' }} · {{ strtoupper($authRole) }}">
        <span class="nav-user-ic"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
        <span class="nav-user-name">{{ $authUser['name'] ?? '' }}</span>
      </span>
      <a href="http://server_update:8000/solist" class="nav-btn">หน้าหลัก</a>
    </div>
  </div>
</header>

<div id="content">
  <div class="filters">
    <div class="fld"><label for="fDate">วันที่</label><input type="date" id="fDate" onchange="applyFilter()"></div>
    <div class="fld fld-sel"><label for="fType">ประเภท</label>
      <select id="fType" onchange="applyFilter()">
        <option value="">ทั้งหมด</option><option value="รับเข้าสต็อก">รับเข้าสต็อก</option><option value="คืนเข้าสต็อก">คืนเข้าสต็อก</option>
        <option value="ขายสินค้าออก">ขายสินค้าออก</option><option value="ยืมสินค้า">ยืมสินค้า</option><option value="เบิกของ">เบิกของ</option>
      </select><i></i>
    </div>
    <div class="fld"><label for="fOp">ผู้ดำเนินงาน</label><input type="search" id="fOp" placeholder="ชื่อผู้ดำเนินงาน" oninput="applyFilter()" autocomplete="off"></div>
    <div class="fld"><label for="fBill">เลขเอกสาร</label><input type="search" id="fBill" placeholder="เช่น PO-, SO-" oninput="applyFilter()" autocomplete="off"></div>
    <div class="fld"><label for="fItemId">รหัสสินค้า</label><input type="search" id="fItemId" placeholder="รหัส" oninput="applyFilter()" autocomplete="off"></div>
    <div class="fld"><label for="fItem">รายการสินค้า</label><input type="search" id="fItem" placeholder="ชื่อสินค้า" oninput="applyFilter()" autocomplete="off"></div>
    <div class="fld"><label for="fShelf">ชั้นวาง</label><input type="search" id="fShelf" placeholder="ชั้นวาง" oninput="applyFilter()" autocomplete="off"></div>
    <div class="f-acts">
    <button class="btn btn-ghost" id="clrBtn" onclick="clearFilter()" hidden>ล้างตัวกรอง</button>
    @if(in_array($authRole, ['admin','user']))
      <button class="btn btn-solid" id="pdfBtn" onclick="generatePDF()" title="เลือกวันที่ก่อน แล้วดาวน์โหลดรายงานของวันนั้น"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg><span id="pdfLbl">ดาวน์โหลด PDF</span></button>
    @endif
    </div>
  </div>

  <div class="tbl-wrap">
    <table>
      <thead><tr>
        <th>วันเวลา</th><th>ผู้ดำเนินงาน</th><th>ประเภท</th><th>เอกสาร</th><th>รายการ</th><th class="num">จำนวน</th><th class="num">ราคา/หน่วย</th><th>ชั้นวาง</th><th>JOB Detail</th><th>รูป</th>
        @if($authRole==='admin' || trim($authUser['name'] ?? '') === 'ชัย')<th>จัดการ</th>@endif
      </tr></thead>
      <tbody id="tb"></tbody>
    </table>
  </div>
  <div id="paging"></div>
</div>

<script>
const CSRF=document.querySelector('meta[name="csrf-token"]').content;
const ROLE=@json($authRole);
const UNAME=@json(strtolower(trim($authUser['name'] ?? '')));   // ชื่อผู้ใช้
// แก้ไข/ลบ: เฉพาะ admin หรือผู้ใช้ชื่อ chai (user สร้างได้อย่างเดียว)
const CAN_EDIT=(ROLE==='admin'||UNAME==='ชัย');
const COLS=CAN_EDIT?11:10;
const PG=100;
const typeMap={'รับเข้าสต็อก':'t-in','คืนเข้าสต็อก':'t-ret','ขายสินค้าออก':'t-sell','ยืมสินค้า':'t-bor','เบิกของ':'t-wit'};
// ทิศทางของจำนวน: รับเข้า/คืน = เพิ่มเข้าคลัง (+), ขาย/ยืม/เบิก = ออกจากคลัง (−)
const TYPE_DIR={'รับเข้าสต็อก':1,'คืนเข้าสต็อก':1,'ขายสินค้าออก':-1,'ยืมสินค้า':-1,'เบิกของ':-1};
const NEST_URL=@json($nestUrl);
const NEST_KEY=@json($nestKey);
let isEditingTxRow = false;

const API={
  async get(u){return(await fetch(u)).json()},
  async put(u,d){return(await fetch(u,{method:'PUT',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify(d)})).json()},
  async del(u){return(await fetch(u,{method:'DELETE',headers:{'X-CSRF-TOKEN':CSRF}})).json()},
};

let pageData=[], pg=1, lastPage=1, total=0;
let _debounce=null;
let progressInterval = null;

function showOv(t){
  document.getElementById('ovText').textContent = t || 'กำลังโหลดข้อมูล...';
  document.getElementById('ov').classList.add('on');
  startProgressSimulation();
}

function hideOv(){
  clearInterval(progressInterval);
  document.getElementById('progressBar').style.width = '100%';
  document.getElementById('ovPercent').textContent = '100%';

  setTimeout(() => {
    document.getElementById('ov').classList.remove('on');
    setTimeout(() => {
      document.getElementById('progressBar').style.width = '0%';
      document.getElementById('ovPercent').textContent = '0%';
    }, 300);
  }, 200);
}

function startProgressSimulation(){
  let progress = 0;
  const bar = document.getElementById('progressBar');
  const text = document.getElementById('ovPercent');

  clearInterval(progressInterval);

  progressInterval = setInterval(() => {
    if (progress < 30) {
      progress += Math.random() * 15;
    } else if (progress < 70) {
      progress += Math.random() * 8;
    } else if (progress < 90) {
      progress += Math.random() * 3;
    } else {
      progress = 90;
    }

    if (progress > 90) progress = 90;

    bar.style.width = Math.floor(progress) + '%';
    text.textContent = Math.floor(progress) + '%';
  }, 150);
}

function openSB(){document.getElementById('sidebar').classList.add('open');document.getElementById('sbOv').classList.add('open')}
function closeSB(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sbOv').classList.remove('open')}
function toast(m,e){const t=document.getElementById('toast');t.textContent=m;t.classList.toggle('err',!!e);t.classList.add('on');clearTimeout(t._t);t._t=setTimeout(()=>t.classList.remove('on'),3200)}
function eh(s){return String(s??'').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;')}
const $=(id)=>document.getElementById(id);

// ============================================================
// ส่วนแสดงรูปภาพ — แก้ CSP: ใช้ img + lh3 thumbnail แทน iframe
// ============================================================

function extractGoogleDriveFileId(url){
  if(!url) return null;
  const patterns = [
    /\/file\/d\/([a-zA-Z0-9_-]+)/,
    /\?id=([a-zA-Z0-9_-]+)/,
    /&id=([a-zA-Z0-9_-]+)/
  ];
  for(let pattern of patterns){
    const match = url.match(pattern);
    if(match && match[1]) return match[1];
  }
  return null;
}

function convertToDirectImageUrl(url){
  const fileId = extractGoogleDriveFileId(url);
  if(fileId){
    return `https://lh3.googleusercontent.com/d/${fileId}`;
  }
  return url;
}

function getGoogleDriveViewUrl(url){
  const fileId = extractGoogleDriveFileId(url);
  if(fileId){
    return `https://drive.google.com/file/d/${fileId}/view`;
  }
  return url;
}

let _currentOriginalUrl = '';

function openGoogleDriveImage(url){
  if(!url){
    toast('รายการนี้ไม่มีรูป', true);
    return;
  }

  _currentOriginalUrl = url;

  const imgEl = document.getElementById('modalImg');
  const loadingEl = document.getElementById('modalLoading');
  const errorEl = document.getElementById('modalError');
  const openLink = document.getElementById('modalOpenLink');
  const fallbackLink = document.getElementById('modalFallbackLink');

  const viewUrl = getGoogleDriveViewUrl(url);
  openLink.href = viewUrl;
  fallbackLink.href = viewUrl;

  imgEl.style.display = 'none';
  errorEl.style.display = 'none';
  loadingEl.style.display = 'block';

  const directUrl = convertToDirectImageUrl(url);

  imgEl.onload = function(){
    loadingEl.style.display = 'none';
    errorEl.style.display = 'none';
    imgEl.style.display = 'block';
  };

  imgEl.onerror = function(){
    loadingEl.style.display = 'none';
    imgEl.style.display = 'none';
    errorEl.style.display = 'block';
  };

  imgEl.src = directUrl;

  document.getElementById('modalOv').classList.add('on');
  document.body.style.overflow = 'hidden';
}

function closeModal(){
  document.getElementById('modalOv').classList.remove('on');
  document.body.style.overflow = '';
  _currentOriginalUrl = '';

  setTimeout(() => {
    const imgEl = document.getElementById('modalImg');
    imgEl.src = '';
    imgEl.style.display = 'none';
    document.getElementById('modalLoading').style.display = 'none';
    document.getElementById('modalError').style.display = 'none';
  }, 300);
  flushTxPendingRefresh();
}

document.addEventListener('keydown', function(e){
  if(e.key === 'Escape') closeModal();
});

document.getElementById('modalOv').addEventListener('click', function(e){
  if(e.target === this) closeModal();
});

// ============================================================

function buildQuery(page,limit){
  const fD=document.getElementById('fDate').value;
  const p=new URLSearchParams();
  p.set('page',page); p.set('limit',limit||PG);
  if(fD){const parts=fD.split('-');p.set('fDate',parts[2]+'/'+parts[1]+'/'+parts[0])}
  const fOp=document.getElementById('fOp').value.trim();    if(fOp) p.set('fOp',fOp);
  const fBill=document.getElementById('fBill').value.trim();if(fBill) p.set('fBill',fBill);
  const fItem=document.getElementById('fItem').value.trim();if(fItem) p.set('fItem',fItem);
  const fItemId=document.getElementById('fItemId').value.trim();if(fItemId) p.set('fItemId',fItemId);
  const fType=document.getElementById('fType').value;        if(fType) p.set('fType',fType);
  const fShelf=document.getElementById('fShelf').value.trim();if(fShelf) p.set('fShelf',fShelf);
  return '/api/transaction?'+p.toString();
}

async function loadPage(page, showLoader){
  if(showLoader) showOv();
  try{
    const res=await API.get(buildQuery(page));
    pageData=res.data||[];
    pg=res.page||1;
    lastPage=res.lastPage||1;
    total=res.total||0;
    render();
  }catch(e){toast('โหลดข้อมูลไม่สำเร็จ: '+e.message,true)}
  if(showLoader) hideOv();
}

const TH_MONTH=['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
// แยก Timestamp "dd/mm/yyyy HH:MM:SS" เป็นวัน + เวลา
function splitTs(ts){
  const m=String(ts||'').match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})\s+(\d{1,2}):(\d{2})/);
  if(!m) return {key:'',label:ts||'-',time:ts||'-'};
  const d=+m[1],mo=+m[2],y=+m[3];
  const now=new Date(),dt=new Date(y,mo-1,d);
  const diff=Math.round((new Date(now.getFullYear(),now.getMonth(),now.getDate())-dt)/864e5);
  const base=`${d} ${TH_MONTH[mo-1]} ${y+543}`;
  const label=diff===0?`วันนี้ ${base}`:diff===1?`เมื่อวาน ${base}`:base;
  return {key:`${y}-${mo}-${d}`,label,time:`${m[4].padStart(2,'0')}:${m[5]}`};
}
function fmtNum(v){
  if(v===''||v==null) return null;
  const n=Number(v);
  return isNaN(n)?String(v):n.toLocaleString('en-US',{maximumFractionDigits:3});
}
function thumbUrl(url){
  const id=extractGoogleDriveFileId(url);
  return id?`https://lh3.googleusercontent.com/d/${id}=w120`:url;
}

function render(){
  const tb=document.getElementById('tb');tb.innerHTML='';
  updateSummary();
  if(!pageData.length){
    const filtered=hasFilter();
    tb.innerHTML=`<tr class="empty-row"><td colspan="${COLS}"><b>${filtered?'ไม่พบรายการที่ตรงกับตัวกรอง':'ยังไม่มีรายการเข้า-ออก'}</b><span>${filtered?'ลองเปลี่ยนวันที่หรือคำค้น หรือกด “ล้างตัวกรอง”':'รายการรับเข้า ขาย ยืม และเบิกของจะแสดงที่นี่'}</span></td></tr>`;
    renderPg();return;
  }

  pageData.forEach((row,i)=>{
    const type=row['ประเภทข้อมูล']||'',tc=typeMap[type]||'';
    const q=fmtNum(row['จำนวน']);
    const dir=TYPE_DIR[type]||0;
    const qtyHtml=q===null?'<span class="muted">-</span>':`<span class="qty ${dir>0?'in':dir<0?'out':''}">${dir>0?'+':dir<0?'−':''}${eh(q)}</span>`;
    const pr=fmtNum(row['ราคาต่อหน่วย']);
    const priceHtml=ROLE==='viewer'||pr===null?'<span class="muted">-</span>':eh(pr);

    const tr=document.createElement('tr');
    tr.className='row'+(i%2?' alt':'');
    tr.dataset.i=i;
    tr.innerHTML=`<td class="t-time">${eh(row['Timestamp']||'-')}</td><td class="t-op">${eh(row['ชื่อผู้ดำเนินงาน']||'-')}</td><td class="${type?'tcell '+tc:''}">${type?eh(type):'<span class="muted">-</span>'}</td><td class="t-bill">${eh(row['หมายเลขเอกสาร']||'-')}</td><td class="t-item">${eh(row['รายการ']||'-')}</td><td class="t-qty">${qtyHtml}</td><td class="num">${priceHtml}</td><td class="t-shelf">${eh(row['ชั้นวาง']||'-')}</td><td class="t-note">${row['หมายเหตุ']?eh(row['หมายเหตุ']):'<span class="muted">-</span>'}</td><td></td>`;

    if(row['รูปประกอบ']){
      const btn=document.createElement('button');
      btn.className='thumb';
      btn.type='button';
      btn.title='ดูรูป';
      btn.setAttribute('aria-label','ดูรูปประกอบ');
      const im=new Image();
      im.alt='';im.loading='lazy';im.decoding='async';
      im.onload=()=>im.classList.add('ready');
      im.onerror=()=>{btn.classList.add('fail');btn.textContent='ดูรูป';};
      im.src=thumbUrl(row['รูปประกอบ']);
      btn.appendChild(im);
      btn.onclick=()=>openGoogleDriveImage(row['รูปประกอบ']);
      tr.cells[9].appendChild(btn);
    }else{
      tr.cells[9].innerHTML='<span class="muted">-</span>';
    }

    if(CAN_EDIT){
      const tdActs=document.createElement('td');
      tdActs.className='acts';
      const editBtn=document.createElement('button');
      editBtn.className='ib a-edit';
      editBtn.textContent='แก้ไข';
      editBtn.onclick=function(){ editRow(i); };
      tdActs.appendChild(editBtn);
      tr.appendChild(tdActs);
    }
    tb.appendChild(tr);
  });
  renderPg();
}

function renderPg(){
  const el=document.getElementById('paging');el.innerHTML='';
  if(lastPage<=1) return;
  const chevL='<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>';
  const chevR='<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>';
  if(pg>1){const b=document.createElement('button');b.className='pg-btn';b.innerHTML=chevL+'ก่อนหน้า';b.onclick=()=>{loadPage(pg-1,false);scrollTo({top:0,behavior:'smooth'})};el.appendChild(b)}
  const s=document.createElement('span');s.className='pg-info';s.innerHTML=`หน้า <b>${pg}</b> จาก ${lastPage}`;el.appendChild(s);
  if(pg<lastPage){const b=document.createElement('button');b.className='pg-btn';b.innerHTML='ถัดไป'+chevR;b.onclick=()=>{loadPage(pg+1,false);scrollTo({top:0,behavior:'smooth'})};el.appendChild(b)}
}

/* ========== สถานะตัวกรอง ========== */
const FIELD_IDS=['fDate','fOp','fBill','fItemId','fItem','fShelf','fType'];
function hasFilter(){return FIELD_IDS.some(id=>($(id).value||'').trim())}
function updateSummary(){
  const f=hasFilter();
  $('sumLine').innerHTML=f?`พบ <b>${total.toLocaleString('en-US')}</b> รายการตามตัวกรอง`:`ทั้งหมด <b>${total.toLocaleString('en-US')}</b> รายการ`;
  $('clrBtn').hidden=!f;
}
function applyFilter(){
  clearTimeout(_debounce);
  _debounce=setTimeout(()=>loadPage(1,false),400);
}
function clearFilter(){
  ['fBill','fItem','fItemId','fDate','fType','fShelf','fOp'].forEach(id=>document.getElementById(id).value='');
  syncSelects();
  loadPage(1,false);
}

function editRow(i){
  if(!CAN_EDIT)return;
  isEditingTxRow = true;
  const row=pageData[i],tr=document.querySelector(`#tb tr[data-i="${i}"]`);if(!tr)return;tr.classList.add('editing');
  const types=['รับเข้าสต็อก','คืนเข้าสต็อก','ขายสินค้าออก','ยืมสินค้า','เบิกของ'];
  tr.innerHTML=`<td class="t-time">${eh(row['Timestamp']||'-')}</td><td><input class="edit-input" id="eOp" value="${(row['ชื่อผู้ดำเนินงาน']||'').replace(/"/g, '&quot;')}"></td><td><select id="eType" class="edit-input">${types.map(t=>`<option ${row['ประเภทข้อมูล']===t?'selected':''}>${t}</option>`).join('')}</select></td><td><input class="edit-input" id="eBill" value="${(row['หมายเลขเอกสาร']||'').replace(/"/g, '&quot;')}"></td><td class="t-item">${eh(row['รายการ']||'-')}</td><td><input class="edit-input" id="eQty" value="${row['จำนวน']||''}" type="number"></td><td><input class="edit-input" id="ePrice" value="${row['ราคาต่อหน่วย']!==''&&row['ราคาต่อหน่วย']!=null?row['ราคาต่อหน่วย']:''}" type="number" step="0.001"></td><td><input class="edit-input" id="eShelf" value="${(row['ชั้นวาง']||'').replace(/"/g, '&quot;')}"></td><td><input class="edit-input" id="eNote" value="${(row['หมายเหตุ']||'').replace(/"/g, '&quot;')}"></td><td><input type="hidden" id="eImg" value="${(row['รูปประกอบ']||'').replace(/"/g, '&quot;')}"><span class="muted">-</span></td><td class="acts"><button class="ib a-save" onclick="saveRow(${i})">บันทึก</button><button class="ib a-del" onclick="delRow(${i})">ลบ</button><button class="ib a-can" onclick="isEditingTxRow=false;loadPage(pg,false);flushTxPendingRefresh()">ยกเลิก</button></td>`;
}

async function saveRow(i){
  const row=pageData[i];
  const op=document.getElementById('eOp').value.trim(),bill=document.getElementById('eBill').value.trim();
  if(!op||!bill){alert('กรุณากรอกข้อมูลให้ครบ');return}
  if(!confirm('ต้องการบันทึก?'))return;
  try{
    await API.put('/api/transaction/'+encodeURIComponent(row.transaction_id),{operator:op,type:document.getElementById('eType').value,bill,quantity:document.getElementById('eQty').value,price:document.getElementById('ePrice').value||'',shelf:document.getElementById('eShelf').value.trim(),note:document.getElementById('eNote').value.trim(),image:document.getElementById('eImg').value.trim(),oldQuantity:row['จำนวน'],oldType:row['ประเภทข้อมูล'],oldItemId:row['item_id'],itemName:row['รายการ']});
    toast('บันทึกสำเร็จ');
    isEditingTxRow=false;
    await loadPage(pg,false);
    flushTxPendingRefresh();
  }catch(e){
    toast(e.message,true);
  }
}

async function delRow(i){
  const row=pageData[i];
  if(!confirm(`ลบรายการ?\nเอกสาร: ${row['หมายเลขเอกสาร']}\nรายการ: ${row['รายการ']}`))return;
  const reason=(prompt('กรุณาระบุเหตุผลในการลบรายการนี้ :','')||'').trim();
  if(!reason){ toast('ต้องระบุเหตุผลในการลบ',true); return; }
  try{
    const dr=await API.del('/api/transaction/'+encodeURIComponent(row.transaction_id)+'?reason='+encodeURIComponent(reason));
    if(dr&&dr.success===false){ toast(dr.error||'ลบไม่สำเร็จ',true); return; }
    toast('ลบเรียบร้อย');
    isEditingTxRow=false;
    await loadPage(pg,false);
    flushTxPendingRefresh();
  }catch(e){
    toast(e.message,true);
  }
}

let _fontN=null,_fontB=null;

async function _fetchFontB64(url){
  const buf=await(await fetch(url)).arrayBuffer();
  const bytes=new Uint8Array(buf);let bin='';
  for(let i=0;i<bytes.length;i+=8192)bin+=String.fromCharCode.apply(null,bytes.subarray(i,i+8192));
  return btoa(bin);
}
async function loadFonts(){
  if(_fontN&&_fontB)return;
  [_fontN,_fontB]=await Promise.all([
    _fetchFontB64('https://cdn.jsdelivr.net/gh/google/fonts@main/ofl/sarabun/Sarabun-Regular.ttf'),
    _fetchFontB64('https://cdn.jsdelivr.net/gh/google/fonts@main/ofl/sarabun/Sarabun-Bold.ttf')
  ]);
}
function normalizeThai(t){return typeof t==='string'?t.normalize('NFC'):String(t??'-')}
function convertDateFormat(ymd){
  if(!ymd)return'-';
  const[y,m,d]=ymd.split('-');
  const months=['','ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
  return`${parseInt(d)} ${months[parseInt(m)]} ${parseInt(y)+543}`;
}

async function generatePDF(){
  const dateEl=document.getElementById('fDate');
  if(!dateEl.value){toast('เลือกวันที่ก่อน แล้วกดดาวน์โหลด PDF อีกครั้ง',true);dateEl.focus();return}
  if(typeof window.jspdf==='undefined'){alert('กำลังโหลด library PDF กรุณารอสักครู่แล้วลองอีกครั้ง');return}
  const btn=document.getElementById('pdfBtn');
  btn.disabled=true;
  const lbl=document.getElementById('pdfLbl');
  const originalLabel=lbl.textContent;
  lbl.textContent='กำลังสร้าง PDF...';
  try{
    const res=await API.get(buildQuery(1,1000000));
    let dataToUse=res.data||[];
    if(!dataToUse.length){toast('วันที่เลือกยังไม่มีรายการ',true);return}

    await loadFonts();
    const{jsPDF}=window.jspdf;
    const doc=new jsPDF('p','mm','a4');
    doc.addFileToVFS('Sarabun-R.ttf',_fontN);doc.addFont('Sarabun-R.ttf','Sarabun','normal');
    doc.addFileToVFS('Sarabun-B.ttf',_fontB);doc.addFont('Sarabun-B.ttf','Sarabun','bold');

    const typeValue=document.getElementById('fType').value||'ทั้งหมด';
    const rawDate=dateEl.value;
    const fileName=`รายงาน(${typeValue})สต็อก-(${rawDate}).pdf`;

    if(typeValue==='ทั้งหมด'){
      const typeOrder={'รับเข้าสต็อก':1,'คืนเข้าสต็อก':2,'ขายสินค้าออก':3,'ยืมสินค้า':4,'เบิกของ':5};
      dataToUse=[...dataToUse].sort((a,b)=>(typeOrder[a['ประเภทข้อมูล']]||999)-(typeOrder[b['ประเภทข้อมูล']]||999));
    }

    const tableBody=dataToUse.map(r=>[
      normalizeThai(r.Timestamp||'-'),
      normalizeThai(r['ชื่อผู้ดำเนินงาน']||'-'),
      normalizeThai(r['ประเภทข้อมูล']||'-'),
      normalizeThai(r['หมายเลขเอกสาร']||'-'),
      normalizeThai(r['รายการ']||'-'),
      normalizeThai(String(r['จำนวน']??'-')),
      normalizeThai(String(r['ราคาต่อหน่วย']??'-')),
      normalizeThai(r['ชั้นวาง']||'-'),
    ]);

    doc.setFont('Sarabun','bold');doc.setFontSize(18);doc.setTextColor(0,0,0);
    doc.text(normalizeThai('3E TRADING'),105,15,{align:'center'});
    doc.setFontSize(14);
    doc.text(normalizeThai(`รายงานประวัติการดำเนินการคลังสินค้า (${typeValue})`),105,23,{align:'center'});
    doc.setFont('Sarabun','normal');doc.setFontSize(12);
    doc.text(normalizeThai(`วันที่รายงาน: ${convertDateFormat(rawDate)}`),15,32);

    doc.autoTable({
      startY:38,
      head:[['เวลา','ผู้ดำเนินงาน','ประเภท','หมายเลขเอกสาร','รายการสินค้า','จำนวน','ราคาต่อหน่วย','ชั้น'].map(normalizeThai)],
      body:tableBody,
      theme:'grid',
      rowPageBreak:'avoid',
      margin:{top:30,bottom:25,left:10,right:10},
      styles:{font:'Sarabun',fontSize:11,cellPadding:{top:4,right:2,bottom:2,left:2},valign:'middle',textColor:[0,0,0],lineColor:[0,0,0],lineWidth:.1,overflow:'linebreak'},
      headStyles:{fillColor:[255,255,255],textColor:[0,0,0],fontStyle:'bold',halign:'center',lineWidth:.2},
      alternateRowStyles:{fillColor:[255,255,255]},
      columnStyles:{0:{cellWidth:28},1:{cellWidth:20,halign:'center'},2:{cellWidth:24,halign:'center'},3:{cellWidth:26},4:{cellWidth:'auto'},5:{cellWidth:14,halign:'center'},6:{cellWidth:26,halign:'right'},7:{cellWidth:14,halign:'center'}},
    });

    const pageCount=doc.internal.getNumberOfPages();
    for(let i=1;i<=pageCount;i++){
      doc.setPage(i);doc.setFont('Sarabun','normal');doc.setFontSize(9);doc.setTextColor(0,0,0);
      doc.text(normalizeThai(`พิมพ์เมื่อ: ${new Date().toLocaleString('th-TH')}`),15,285);
      doc.text(normalizeThai(`หน้า ${i} จาก ${pageCount}`),195,285,{align:'right'});
    }
    doc.save(fileName);
    toast('ดาวน์โหลด PDF แล้ว: '+fileName);
  }catch(err){
    console.error(err);
    toast('สร้าง PDF ไม่สำเร็จ: '+err.message,true);
  }finally{
    btn.disabled=false;
    lbl.textContent=originalLabel;
  }
}

/* ========== สลับธีมมืด/สว่าง (ใช้ค่าร่วมกับหน้าค้นหาสินค้า) ========== */
function applyTheme(t){
  document.documentElement.dataset.theme=t;
  try{localStorage.setItem('3e-theme',t);}catch(e){}
  const m=$('metaTheme'); if(m) m.content=t==='dark'?'#111214':'#FFFFFF';
}
function toggleTheme(e){
  const next=document.documentElement.dataset.theme==='dark'?'light':'dark';
  const reduce=window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if(!document.startViewTransition||reduce){applyTheme(next);return;}
  const r=e.currentTarget.getBoundingClientRect();
  const x=r.left+r.width/2,y=r.top+r.height/2;
  const end=Math.hypot(Math.max(x,innerWidth-x),Math.max(y,innerHeight-y));
  const vt=document.startViewTransition(()=>applyTheme(next));
  vt.ready.then(()=>document.documentElement.animate(
    {clipPath:[`circle(0px at ${x}px ${y}px)`,`circle(${end}px at ${x}px ${y}px)`]},
    {duration:650,easing:'cubic-bezier(.2,.7,.2,1)',pseudoElement:'::view-transition-new(root)'}));
}
window.addEventListener('scroll',()=>$('hdr').classList.toggle('scrolled',scrollY>4),{passive:true});

// โหลดข้อมูลครั้งแรกตอนเข้าหน้า — จุดเดียวที่แสดง overlay progress bar
/* ========== ตัวเลือกแบบเด้งลง: ใช้แทนหน้าตา select เดิม (ค่า/onchange ยังอยู่ที่ select ตัวเดิม) ========== */
const DD_LIST=[];
function enhanceSelect(sel,fmt){
  const fld=sel.closest('.fld');
  const view=o=>fmt?fmt(o.value,o.text):eh(o.text);
  const btn=document.createElement('button');
  btn.type='button';btn.className='dd-btn';btn.id=sel.id+'Btn';
  btn.setAttribute('aria-haspopup','listbox');btn.setAttribute('aria-expanded','false');
  const pop=document.createElement('div');
  pop.className='dd-pop';pop.hidden=true;pop.setAttribute('role','listbox');
  sel.tabIndex=-1;sel.setAttribute('aria-hidden','true');
  sel.after(btn);fld.appendChild(pop);
  const lb=fld.querySelector('label');if(lb) lb.htmlFor=btn.id;
  const opts=()=>[...pop.querySelectorAll('.dd-opt')];
  function sync(){
    const o=sel.options[sel.selectedIndex]||sel.options[0];
    btn.innerHTML=`<span class="dd-val">${view(o)}</span>`;
    btn.title=o.text;
  }
  function open(){
    DD_LIST.forEach(d=>d!==api&&d.close());
    pop.innerHTML=[...sel.options].map((o,i)=>
      `<button type="button" class="dd-opt${o.selected?' on':''}${!o.value?' dd-all':''}" data-i="${i}" role="option" aria-selected="${o.selected}">${view(o)}</button>`+(i===0&&!o.value?'<div class="dd-sep"></div>':'')).join('');
    pop.hidden=false;pop.classList.remove('to-right');
    if(pop.getBoundingClientRect().right>innerWidth-8) pop.classList.add('to-right');
    fld.classList.add('open');btn.setAttribute('aria-expanded','true');
    (pop.querySelector('.dd-opt.on')||pop.querySelector('.dd-opt'))?.focus({preventScroll:true});
  }
  function close(){
    if(pop.hidden) return;
    pop.hidden=true;fld.classList.remove('open');btn.setAttribute('aria-expanded','false');
  }
  function choose(i){
    const changed=sel.selectedIndex!==i;
    sel.selectedIndex=i;sync();close();btn.focus();
    if(changed) sel.dispatchEvent(new Event('change',{bubbles:true}));
  }
  btn.addEventListener('click',()=>pop.hidden?open():close());
  btn.addEventListener('keydown',e=>{if(['ArrowDown','ArrowUp'].includes(e.key)){e.preventDefault();open();}});
  pop.addEventListener('mousedown',e=>e.preventDefault());
  pop.addEventListener('click',e=>{const o=e.target.closest('.dd-opt');if(o) choose(+o.dataset.i);});
  pop.addEventListener('keydown',e=>{
    const list=opts(),i=list.indexOf(document.activeElement);
    if(e.key==='ArrowDown'){e.preventDefault();list[Math.min(i+1,list.length-1)]?.focus();}
    else if(e.key==='ArrowUp'){e.preventDefault();list[Math.max(i-1,0)]?.focus();}
    else if(e.key==='Home'){e.preventDefault();list[0]?.focus();}
    else if(e.key==='End'){e.preventDefault();list[list.length-1]?.focus();}
    else if(e.key==='Escape'){e.preventDefault();e.stopPropagation();close();btn.focus();}
    else if(e.key==='Tab'){close();}
  });
  document.addEventListener('mousedown',e=>{if(!fld.contains(e.target)) close();});
  sel.addEventListener('change',sync);
  const api={sync,close};
  DD_LIST.push(api);
  sync();
  return api;
}
// เรียกหลังตั้งค่า select ด้วยโค้ด (เช่น ล้างตัวกรอง) ให้ปุ่มแสดงค่าล่าสุด
function syncSelects(){DD_LIST.forEach(d=>d.sync());}
const DD_ALL_IC='<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>';
// ประเภท: จุดสีตามประเภท (สีเดียวกับช่องประเภทในตาราง)
enhanceSelect($('fType'),(v,t)=>v
  ?`<span class="dd-dot ${typeMap[v]||''}"></span><span class="dd-name">${eh(t)}</span>`
  :`<span class="dd-ic">${DD_ALL_IC}</span><span class="dd-name">ทั้งหมด</span>`);

loadPage(1,true);

// ══════════════ REALTIME (SSE) ══════════════
let txRefreshPending = false;
let txRefreshDebounce = null;

function isBusyEditingTx(){
  return document.getElementById('modalOv').classList.contains('on') || isEditingTxRow;
}

function requestTxSilentRefresh(){
  clearTimeout(txRefreshDebounce);
  txRefreshDebounce = setTimeout(() => {
    if (isBusyEditingTx()) { txRefreshPending = true; return; }
    loadPage(pg, false);
  }, 300);
}

function flushTxPendingRefresh(){
  if (!txRefreshPending || isBusyEditingTx()) return;
  txRefreshPending = false;
  loadPage(pg, false);
}

function connectTransactionSSE(){
  const url = `${NEST_URL}/transaction/events?key=${encodeURIComponent(NEST_KEY)}`;
  const es = new EventSource(url);
  es.onmessage = (e) => {
    if (e.data === 'heartbeat') return;
    if (e.data === 'transaction' || e.data === 'items') requestTxSilentRefresh();
  };
  es.onerror = () => console.warn('[SSE transaction] connection issue, browser will auto-retry');
}
connectTransactionSSE();
</script>
</body>
</html>