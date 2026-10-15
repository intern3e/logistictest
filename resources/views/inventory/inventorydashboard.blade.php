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
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <meta name="theme-color" content="#FFFFFF" id="metaTheme">
  <script>(function(){var t="light";try{t=localStorage.getItem("3e-theme")||"light"}catch(e){}document.documentElement.dataset.theme=t;})();</script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11.1.14/swiper-bundle.min.css">
  <script src="https://cdn.jsdelivr.net/npm/swiper@11.1.14/swiper-bundle.min.js" defer></script>
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* =====================================================================
       3E TRADING · ค้นหาสินค้า (ตาราง จัดการสินค้า)
       ธีมสว่างเป็นค่าเริ่มต้น (สลับเป็นมืดได้ด้วยปุ่มบนแถบบน ใช้ค่าร่วมกับหน้าอื่น)
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


    /* ===================== หน้า ค้นหาสินค้า (ตาราง) ===================== */
    #content{max-width:1880px;margin:0 auto;padding:24px 32px 64px}
    .hamburger{display:flex;margin-right:6px} /* ปุ่มเมนูอยู่ซ้ายสุดเสมอ */
    /* หัวข้อหน้า + จำนวนรายการ บนแถบบน */
    .nav-title{flex-shrink:0;display:flex;flex-direction:column;justify-content:center;line-height:1.25;margin:0 14px 0 2px;padding:0 18px;border-left:1px solid var(--line);border-right:1px solid var(--line)}
    .nav-title b{font-size:14.5px;font-weight:600;white-space:nowrap}
    .nav-title span{font-size:12.5px;color:var(--slate);white-space:nowrap}
    .nav-title span b{font-size:12.5px;color:var(--ink)}

    /* ปุ่ม */
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;height:38px;padding:0 15px;border:none;border-radius:999px;font-size:13.5px;font-weight:600;white-space:nowrap;cursor:pointer;transition:background .2s,border-color .2s,transform .2s var(--ease),box-shadow .2s,color .2s}
    .btn svg{flex-shrink:0}
    .btn[hidden]{display:none}
    .btn-ghost{background:var(--surface);color:var(--ink);border:1px solid var(--line)}
    .btn-ghost:hover{border-color:var(--ink)}
    .btn-solid{background:var(--btn);color:var(--btn-ink)}
    .btn-solid:hover{background:var(--btn-hover);transform:translateY(-1px);box-shadow:0 10px 24px -12px rgba(0,0,0,.4)}
    .btn-top{background:var(--top-bg);color:var(--top-ink);border:1px solid transparent}
    .btn-top:hover{border-color:var(--top-ink)}
    .btn-top.on{background:var(--accent);color:#fff}

    /* แผงตัวกรอง */
    .filters{position:relative;z-index:20;display:grid;grid-template-columns:150px 1.5fr 1fr 1fr 175px 160px auto;margin:0 0 22px;background:var(--surface);border:1px solid var(--line-soft);border-radius:16px;box-shadow:0 0 0 1px var(--card-ring),var(--card-shadow,none)}
    .fld{position:relative;display:flex;flex-direction:column;gap:2px;padding:10px 16px 9px;min-width:0}
    .fld+.fld::before{content:'';position:absolute;left:0;top:12px;bottom:12px;width:1px;background:var(--line-soft)}
    .fld:focus-within::after{content:'';position:absolute;left:16px;right:16px;bottom:0;height:2px;border-radius:2px;background:var(--accent)}
    .fld label{font-size:12px;font-weight:500;color:var(--slate)}
    .fld input{width:100%;height:26px;border:none;outline:none;background:transparent;font-size:14px;color:var(--ink)}
    .fld input::placeholder{color:var(--mute)}
    .fld input::-webkit-search-cancel-button{display:none}
    .fld select{width:100%;height:26px;padding-right:18px;border:none;outline:none;background:transparent;font-size:14px;color:var(--ink);cursor:pointer;appearance:none;-webkit-appearance:none}
    .fld select option{color:#1D2023}
    .fld-sel>i{position:absolute;right:18px;bottom:19px;width:6px;height:6px;border-right:1.6px solid var(--slate);border-bottom:1.6px solid var(--slate);transform:rotate(45deg);pointer-events:none}
    .fld .mini-load{position:absolute;right:14px;top:12px;margin:0;width:14px;height:14px}
    /* ยี่ห้อ: รายการเด้งลง */
    .seg-brand .row-in{display:flex;align-items:center;gap:6px}
    .seg-brand .chev{flex-shrink:0;color:var(--mute);transition:transform .25s var(--ease),color .2s}
    .seg-brand.open .chev{transform:rotate(180deg);color:var(--ink)}
    .seg-brand .bdd{top:calc(100% + 6px);left:0;width:100%}
    .f-acts{display:flex;align-items:center;justify-content:flex-end;gap:8px;padding:8px 12px}
    .fld label .kbd{margin-left:6px;padding:0 6px;font-family:inherit;font-size:10.5px;font-weight:600;line-height:16px;color:var(--slate);background:var(--surface);border:1px solid var(--line);border-radius:5px}
    .fld:focus-within .kbd{display:none}
    @media(hover:none){.fld label .kbd{display:none}}

    /* ตาราง: เส้นตาราง + สีสลับแถว */
    :root{--grid:#E3E5E1;--th-bg:#F1F3F6;--th-ink:#2B3036;--th-line:#CDD2D9;--zebra:#FAFAF8;--row-hover:#EEF3FF;--sub-bg:#F7F8F6;--top-bg:#E8EEFC;--top-ink:#1A4FD6}
    :root[data-theme="dark"]{--grid:rgba(255,255,255,.08);--th-bg:#24272B;--th-ink:#E9EAEB;--th-line:rgba(255,255,255,.16);--zebra:rgba(255,255,255,.025);--row-hover:rgba(91,140,255,.08);--sub-bg:rgba(255,255,255,.035);--top-bg:rgba(91,140,255,.14);--top-ink:#A9C1FF}
    .tbl-wrap{background:var(--surface);border:1px solid var(--th-line);border-radius:14px;overflow-x:auto;box-shadow:0 0 0 1px var(--card-ring),var(--card-shadow,none)}
    #dt{min-width:1320px;width:100%;border-collapse:separate;border-spacing:0;font-size:14px}
    #dt th{padding:13px 16px;background:var(--th-bg);border-bottom:2px solid var(--th-line);border-right:1px solid var(--grid);text-align:center;font-size:13px;font-weight:600;color:var(--th-ink);white-space:nowrap}
    #dt td{padding:12px 16px;border-bottom:1px solid var(--grid);border-right:1px solid var(--grid);vertical-align:middle;color:var(--ink)}
    #dt th:last-child,#dt td:last-child{border-right:none}
    #dt tbody tr:last-child td{border-bottom:none}
    #dt tbody tr.row td{transition:background .15s}
    #dt tbody tr.row.alt td{background:var(--zebra)}
    #dt tbody tr.row:hover td{background:var(--row-hover)}
    #dt td.c{text-align:center}
    .qty{font-weight:700;font-size:15px}
    .qty.zero{color:var(--alert)}
    .loc{color:var(--ink-2)}
    /* รายการย่อย */
    #dt tr.sub-row td{background:var(--sub-bg);font-size:13.5px;padding-top:10px;padding-bottom:10px}
    #dt tr.sub-row:hover td{background:var(--row-hover)}
    #dt tr.sub-row td.sub-id{padding-left:52px;color:var(--slate);white-space:nowrap;position:relative}
    /* เส้นโยงรายการย่อย: เส้นตั้งต่อกันตลอดจากปุ่มรายการหลักลงมา แตกกิ่งเข้าแต่ละรายการ ใบสุดท้ายเป็นมุมโค้ง */
    :root{--tree:#9AA1A9} :root[data-theme="dark"]{--tree:rgba(255,255,255,.34)}
    #dt tr.row td:first-child{position:relative}
    #dt tr.row.exp td:first-child::after{content:'';position:absolute;left:28px;top:calc(50% + 13px);bottom:-1px;width:2px;background:var(--tree)}
    #dt tr.sub-row td.sub-id::before{content:'';position:absolute;left:28px;top:-1px;bottom:-1px;width:2px;background:var(--tree)}
    #dt tr.sub-row td.sub-id::after{content:'';position:absolute;left:28px;top:calc(50% - 1px);width:16px;height:2px;background:var(--tree)}
    #dt tr.sub-row.last td.sub-id::before{bottom:auto;height:calc(50% + 2px);width:16px;background:none;border-left:2px solid var(--tree);border-bottom:2px solid var(--tree);border-bottom-left-radius:9px}
    #dt tr.sub-row.last td.sub-id::after{display:none}
    tr.sub-row.hide,tr.sub-form-row.hide{display:none}
    #dt tr.sub-form-row td{background:var(--surface-2);padding:12px 16px;overflow:visible;white-space:normal}
    .sf{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
    .sf-id{font-size:12.5px;color:var(--slate);white-space:nowrap}
    .sf-id b{color:var(--ink)}
    .sf-id i{font-style:normal;color:var(--accent);font-weight:600}
    .sf .finput{flex:1;max-width:240px}
    /* ช่องรหัส */
    .id-cell{display:flex;align-items:center;gap:6px;white-space:nowrap}
    .id-cell strong{font-weight:600}
    .expand-btn,.add-sub-btn,.exp-gap{flex-shrink:0;width:26px;height:26px}
    /* ปุ่มรายการย่อย แบบเรียบ ดูง่าย: ▶ เทา = แสดง/ซ่อนรายการย่อย, + เขียว = เพิ่มรายการย่อย */
    :root{--exp-bg:#E5E7EB;--exp-bg-h:#D1D5DB;--exp-ink:#374151;--add:#10B981;--add-h:#059669}
    :root[data-theme="dark"]{--exp-bg:#33373C;--exp-bg-h:#40454B;--exp-ink:#E5E7EB;--add:#10B981;--add-h:#34D399}
    .expand-btn,.add-sub-btn{display:grid;place-items:center;border:none;border-radius:6px;cursor:pointer;transition:background .15s,transform .15s}
    .expand-btn{background:var(--exp-bg);color:var(--exp-ink)}
    .expand-btn:hover{background:var(--exp-bg-h)}
    .expand-btn svg{transition:transform .2s var(--ease)}
    .expand-btn.open svg{transform:rotate(90deg)}
    .add-sub-btn{background:var(--add);color:#fff}
    .add-sub-btn:hover{background:var(--add-h)}
    .expand-btn:active,.add-sub-btn:active{transform:scale(.92)}
    .rank{display:inline-grid;place-items:center;min-width:34px;height:24px;padding:0 6px;border-radius:999px;background:var(--accent);color:#fff;font-size:12px;font-weight:700}
    .name-link{color:var(--ink);font-weight:600;cursor:pointer;text-decoration:none;background:none;border:none;padding:0;text-align:left;font-size:inherit}
    .name-link:hover{color:var(--accent);text-decoration:underline;text-underline-offset:3px}
    .sell{margin-left:8px}
    /* ป้าย */
    .badge-wrap{display:flex;flex-wrap:wrap;gap:5px}
    /* ประเภท / บริษัท: สีเต็มช่อง (แบบเดียวกับช่องประเภทในหน้ารายการเข้า-ออก) */
    #dt td.fill{background:var(--tfill,#8A9096)!important;color:#fff;text-align:center;font-weight:600;font-size:13px;white-space:nowrap}
    #dt td.fill b{font-weight:700}
    #dt td.fill.f-klang{--tfill:#1E9E62} #dt td.fill.f-asset{--tfill:#6D4BD1}
    #dt td.fill.c-3e{--tfill:#2F6BD8} #dt td.fill.c-3in{--tfill:#0E8A9A} #dt td.fill.c-3em{--tfill:#8E44AD} #dt td.fill.c-3el{--tfill:#D49A12}
    #dt td.fill.c-hd{--tfill:#D8392E} #dt td.fill.c-ep{--tfill:#C93A85} #dt td.fill.c-3p{--tfill:#2E8B57} #dt td.fill.c-aet{--tfill:#5E6670}
    :root[data-theme="dark"] #dt td.fill{filter:saturate(.9) brightness(.88)}
    .muted{color:var(--mute)}
    #dt td.co{text-align:center;white-space:nowrap;color:var(--ink-2)}
    #dt td.co b{color:var(--ink);font-weight:700}
    #dt td.cat{text-align:center;color:var(--ink-2)}
    .badge{display:inline-flex;align-items:center;height:24px;padding:0 9px;border-radius:999px;font-size:12px;font-weight:600;white-space:nowrap;background:var(--bb,var(--surface-2));color:var(--bc,var(--ink-2))}
    .b-klang{--bb:#E4F4EC;--bc:#13704A}.b-asset{--bb:#EEE9FB;--bc:#5B3BB0}
    .b-3e{--bb:#E6EEFB;--bc:#1F4FA8}.b-3in{--bb:#E4F4EC;--bc:#13704A}.b-3em{--bb:#EEE9FB;--bc:#5B3BB0}.b-3el{--bb:#FBF1DA;--bc:#7E5806}
    .b-hd{--bb:#FBE7E5;--bc:#A12A22}.b-ep{--bb:#FBE6F0;--bc:#9D1F5A}.b-3p{--bb:#E6F5EA;--bc:#1C6B35}.b-all{--bb:var(--surface-2);--bc:var(--ink-2)}
    :root[data-theme="dark"] .b-klang,:root[data-theme="dark"] .b-3in{--bb:rgba(43,182,115,.14);--bc:#7FDDB3}
    :root[data-theme="dark"] .b-asset,:root[data-theme="dark"] .b-3em{--bb:rgba(150,120,255,.16);--bc:#C3B2FF}
    :root[data-theme="dark"] .b-3e{--bb:rgba(90,140,255,.15);--bc:#A9C4FF}
    :root[data-theme="dark"] .b-3el{--bb:rgba(226,170,37,.14);--bc:#F6CF72}
    :root[data-theme="dark"] .b-hd{--bb:rgba(229,72,61,.15);--bc:#FF9F94}
    :root[data-theme="dark"] .b-ep{--bb:rgba(235,90,160,.15);--bc:#FFA3CF}
    :root[data-theme="dark"] .b-3p{--bb:rgba(43,182,115,.12);--bc:#8FE0AE}
    /* ปุ่มในตาราง */
    .acts{display:flex;flex-wrap:wrap;gap:6px}
    .ib{height:32px;padding:0 13px;border:1px solid var(--line);border-radius:999px;background:var(--surface);color:var(--ink);font-size:12.5px;font-weight:600;white-space:nowrap;cursor:pointer;transition:border-color .2s,background .2s,color .2s}
    .ib:hover{border-color:var(--ink)}
    .ib.a-save{background:var(--btn);border-color:var(--btn);color:var(--btn-ink)}
    .ib.a-del{color:var(--alert)}
    .ib.a-del:hover{border-color:var(--alert)}
    /* แถวที่กำลังแก้ไข/เพิ่ม */
    #dt tr.editing td{background:var(--surface-2);padding:10px 8px;overflow:visible;white-space:normal}
    #dt tr.editing td:first-child{padding-left:16px}
    .stack{display:flex;flex-direction:column;gap:6px}
    .finput{width:100%;min-width:64px;height:34px;padding:0 10px;border:1px solid var(--line);border-radius:9px;background:var(--surface);color:var(--ink);font-size:13px;transition:border-color .2s,box-shadow .2s}
    select.finput{padding-right:6px}
    .finput:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-ring)}
    .finput[readonly]{background:var(--surface-2);color:var(--mute)}
    .auto{font-size:12px;color:var(--mute);font-style:normal}
    /* ช่องแนะนำ ยี่ห้อ/สถานที่ ตอนแก้ไข */
    ul.ac{list-style:none;margin:4px 0 0;padding:6px;position:absolute;z-index:3000;min-width:180px;max-height:220px;overflow-y:auto;background:var(--surface);border:1px solid var(--line-soft);border-radius:12px;box-shadow:var(--shadow-lift)}
    ul.ac li{padding:8px 10px;border-radius:8px;cursor:pointer;font-size:13px;color:var(--ink)}
    ul.ac li:hover{background:var(--surface-2)}
    .empty-row td{padding:72px 16px!important;text-align:center;border-bottom:none}
    .empty-row b{display:block;font-size:17px;font-weight:600}
    .empty-row span{display:block;margin-top:6px;color:var(--slate)}
    /* เลขหน้า */
    #paging{display:flex;justify-content:center;align-items:center;gap:10px;padding-top:32px}
    .pg-btn{display:inline-flex;align-items:center;gap:6px;height:44px;padding:0 20px;border:1px solid var(--line);border-radius:999px;background:var(--surface);color:var(--ink);font-size:14px;font-weight:600;cursor:pointer;transition:border-color .2s,box-shadow .2s}
    .pg-btn:hover{border-color:var(--accent);box-shadow:0 0 0 4px var(--accent-ring)}
    .pg-info{padding:0 14px;font-size:14px;color:var(--slate)}
    .pg-info b{color:var(--ink);font-weight:600}

    @media(max-width:1280px){
      .filters{grid-template-columns:repeat(3,minmax(0,1fr))}
      .f-acts{grid-column:1/-1;border-top:1px solid var(--line-soft)}
      .fld:nth-child(3n+1)::before{display:none}
      .fld:nth-child(n+4){border-top:1px solid var(--line-soft)}
    }
    @media(max-width:760px){
      #content{padding:22px 16px 44px}
      .nav-title{order:3;flex:1 1 100%;flex-direction:row;justify-content:flex-start;align-items:baseline;gap:10px;margin:0;padding:0;border:none}
      .filters{grid-template-columns:repeat(2,minmax(0,1fr))}
      .fld:nth-child(3n+1)::before{display:block}
      .fld:nth-child(2n+1)::before{display:none}
      .fld:nth-child(n+3){border-top:1px solid var(--line-soft)}
      .f-acts{flex-wrap:wrap}
      .f-acts .btn{flex:1}
      .pg-btn{padding:0 14px}
    }
    /* ประเภทในหน้าต่างประวัติ: สีเต็มช่อง */
    .tx-tbl td.tcell{background:var(--tfill);color:#fff;text-align:center;font-weight:600;white-space:nowrap}
    .tx-tbl tbody tr:hover td.tcell{background:var(--tfill)}
    .tx-tbl td.tcell.t-in{--tfill:#1E9E62}.tx-tbl td.tcell.t-ret{--tfill:#1F9BBC}.tx-tbl td.tcell.t-sell{--tfill:#D8392E}.tx-tbl td.tcell.t-bor{--tfill:#D49A12}.tx-tbl td.tcell.t-wit{--tfill:#E37F0A}
    .tx-tbl td.tcell:not([class*="t-"]){--tfill:var(--mute)}

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

    /* ===================== Top 200 ขายดี: การ์ดเลื่อนแบบ Coverflow ===================== */
    .top-wrap{position:relative;margin:0 0 24px;padding:8px;border:1px solid var(--line-soft);border-radius:32px;background:var(--surface);box-shadow:0 0 0 1px var(--card-ring),var(--card-shadow,none)}
    .top-wrap[hidden]{display:none}
    .top-in{position:relative;padding:22px 8px 10px;border:1px solid var(--line-soft);border-radius:26px;background:linear-gradient(180deg,var(--surface-2),var(--surface) 70%);overflow:hidden}
    .top-badge{position:absolute;left:22px;top:20px;display:inline-flex;align-items:center;gap:7px;height:32px;padding:0 12px;border:1px solid var(--line);border-radius:12px;background:var(--surface);font-size:13.5px;font-weight:600}
    .top-badge svg{color:var(--accent)}
    .top-head{padding:44px 16px 4px;text-align:center}
    .top-head h2{font-size:30px;font-weight:700;letter-spacing:-.01em;line-height:1.2}
    .top-head p{margin-top:4px;font-size:14px;color:var(--slate)}
    .top-swiper{width:100%;padding:26px 0 54px!important}
    .top-swiper .swiper-slide{width:300px;height:auto}
    .tcard{display:flex;flex-direction:column;width:100%;height:100%;padding:0;border:1px solid var(--line-soft);border-radius:24px;background:var(--surface);color:var(--ink);text-align:left;cursor:grab;overflow:hidden;box-shadow:0 18px 40px -22px rgba(0,0,0,.45);transition:box-shadow .3s,transform .3s var(--ease)}
    .swiper-slide-active .tcard{box-shadow:0 0 0 2px var(--accent),0 26px 54px -24px rgba(0,0,0,.5)}
    .tc-media{position:relative;aspect-ratio:1/1;background:var(--media);overflow:hidden}
    .tc-media img{width:100%;height:100%;object-fit:cover;display:block;opacity:0;transition:opacity .5s}
    .tc-media img.ready{opacity:1}
    .tc-ph{position:absolute;inset:0;display:grid;place-items:center;align-content:center;gap:8px;color:var(--ph-ink);font-size:12.5px;background:linear-gradient(135deg,var(--ph-a),var(--ph-b))}
    .tc-ph.loading svg{animation:tcPulse 1.4s ease-in-out infinite}
    @keyframes tcPulse{50%{opacity:.4}}
    .tc-rank{position:absolute;left:12px;top:12px;min-width:46px;height:34px;padding:0 10px;border-radius:12px;display:grid;place-items:center;background:var(--accent);color:#fff;font-size:16px;font-weight:700;box-shadow:0 8px 18px -8px rgba(0,0,0,.5)}
    .tc-rank.r1{background:linear-gradient(135deg,#F6C445,#D99A0B)}
    .tc-rank.r2{background:linear-gradient(135deg,#C9CFD6,#8E98A3)}
    .tc-rank.r3{background:linear-gradient(135deg,#E3A06B,#B5642A)}
    .tc-sell{position:absolute;right:12px;top:12px;height:30px;padding:0 11px;border-radius:999px;display:inline-flex;align-items:center;gap:5px;background:rgba(255,255,255,.92);color:#1D2023;font-size:12.5px;font-weight:600;box-shadow:0 6px 14px -8px rgba(0,0,0,.5)}
    .tc-sell b{color:#D8392E}
    .tc-body{display:flex;flex-direction:column;gap:6px;padding:14px 16px 16px}
    .tc-name{font-size:15px;font-weight:600;line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:40px}
    .tc-meta{display:flex;align-items:center;justify-content:space-between;gap:8px;font-size:12.5px;color:var(--slate)}
    .tc-meta strong{color:var(--ink);font-weight:700;font-size:14px}
    .tc-meta strong.zero{color:var(--alert)}
    /* ปุ่มลูกศร + จุดบอกตำแหน่ง */
    .top-swiper .swiper-button-prev,.top-swiper .swiper-button-next{width:44px;height:44px;margin-top:-36px;border:1px solid var(--line);border-radius:999px;background:var(--surface);color:var(--ink);box-shadow:0 10px 24px -14px rgba(0,0,0,.5);transition:border-color .2s,color .2s}
    .top-swiper .swiper-button-prev:hover,.top-swiper .swiper-button-next:hover{border-color:var(--accent);color:var(--accent)}
    .top-swiper .swiper-button-prev::after,.top-swiper .swiper-button-next::after{font-size:15px;font-weight:700}
    .top-swiper .swiper-pagination-bullet{background:var(--slate)}
    .top-swiper .swiper-pagination-bullet-active{background:var(--accent)}
    .top-count{position:absolute;right:22px;top:20px;height:32px;padding:0 12px;border-radius:12px;display:inline-flex;align-items:center;background:var(--surface);border:1px solid var(--line);font-size:13px;color:var(--slate)}
    .top-count b{color:var(--ink);margin:0 3px}
    .top-list-h{margin:0 0 10px 4px;font-size:15px;font-weight:600}
    .top-list-h[hidden],.tbl-wrap[hidden],.top-swiper[hidden],.top-empty[hidden]{display:none}
    .top-head p b{color:var(--accent);font-weight:600}
    .top-empty{display:flex;flex-direction:column;align-items:center;gap:6px;padding:56px 16px 64px;text-align:center}
    .top-empty b{font-size:17px;font-weight:600}
    .top-empty span{color:var(--slate)}
    /* แคตตาล็อกสินค้า */
    .cat-wrap{margin:8px 0 24px}
    .cat-wrap[hidden]{display:none}
    .cat-head{display:flex;align-items:baseline;justify-content:space-between;gap:12px;margin:0 4px 14px}
    .cat-head h3{font-size:20px;font-weight:700;letter-spacing:-.01em}
    .cat-head span{font-size:13.5px;color:var(--slate)}
    .cat-head span b{color:var(--ink)}
    .cat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:18px}
    .ccard{display:flex;flex-direction:column;border:1px solid var(--line-soft);border-radius:18px;background:var(--surface);overflow:hidden;box-shadow:0 0 0 1px var(--card-ring),var(--card-shadow,none);transition:transform .25s var(--ease),box-shadow .25s}
    .ccard:hover{transform:translateY(-3px);box-shadow:0 0 0 1px var(--card-ring),var(--shadow-lift)}
    .ccard .tc-body{padding:12px 14px 14px;gap:5px}
    .ccard .tc-name{font-size:14px;min-height:38px}
    .ccard .tc-meta{flex-direction:column;align-items:flex-start;gap:2px}
    .ccard .tc-meta>span:first-child{max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .cat-wrap{scroll-margin-top:96px}
    @media(max-width:760px){.cat-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.cat-head h3{font-size:18px}}
    /* ถ้าโหลด Swiper ไม่ได้: เลื่อนแนวนอนธรรมดา */
    .top-swiper.no-swiper .swiper-wrapper{display:flex;gap:20px;overflow-x:auto;scroll-snap-type:x mandatory;padding:0 16px 8px}
    .top-swiper.no-swiper .swiper-slide{flex:0 0 280px;scroll-snap-align:center}
    .top-swiper.no-swiper .swiper-button-prev,.top-swiper.no-swiper .swiper-button-next,.top-swiper.no-swiper .swiper-pagination{display:none}
    @media(max-width:760px){
      .top-wrap{border-radius:24px;padding:6px}
      .top-in{border-radius:20px}
      .top-head h2{font-size:24px}
      .top-count{display:none}
      .top-swiper .swiper-slide{width:250px}
      .top-swiper .swiper-button-prev,.top-swiper .swiper-button-next{display:none}
    }
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

<div class="toast" id="toast" role="status" aria-live="polite"></div>

<!-- ประวัติเข้า-ออกของสินค้า -->
<div class="tx-ov" id="txOv" role="dialog" aria-modal="true" aria-labelledby="txTitle">
  <div class="tx-modal">
    <div class="tx-head">
      <div class="tx-head-t"><h3 id="txTitle">ประวัติเข้า-ออก</h3><span class="tx-badge" id="txId">-</span><span id="txName"></span></div>
      <button class="tx-xbtn" onclick="closeTx()" aria-label="ปิด"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div class="tx-body" id="txBody"><div class="tx-spin"><div class="tx-spin-inner"></div></div></div>
    <div class="tx-foot" id="txFoot">กำลังโหลด...</div>
  </div>
</div>

<div class="sb-ov" id="sbOv" onclick="closeSB()"></div>
<div class="sidebar" id="sidebar">
 <div class="sb-head"><img src="https://lh3.googleusercontent.com/d/1qruaZSyb6gXrJ1Bc_l-p50LdZ6mszbE0" alt="Logo"><span>3E TRADING</span><button class="sb-close" onclick="closeSB()" aria-label="ปิดเมนู">&#10005;</button></div>
  <div class="sb-nav">
    <div class="sb-sec">เมนู</div>
    <a class="sb-item" target="_blank" href="{{ route('inventory.transaction') }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>รายการสินค้า เข้า-ออก</a>
    <a class="sb-item cur" id="sbSearch" href="{{ route('inventory.item') }}" onclick="return goSearchView(event)"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>ค้นหาสินค้า</a>
    <a class="sb-item" id="sbTop" href="#top200" onclick="return goTopView(event)"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>Top 200 ขายดี</a>
    <a class="sb-item" target="_blank" href="{{ route('inventory.vehicles') }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>รถบริษัท</a>
    @if($authRole === 'admin')
    <div class="sb-sec">รายงาน</div>
    <a class="sb-item" target="_blank" href="{{ route('inventory.analyze') }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>วิเคราะห์สินค้า</a>
    <a class="sb-item" target="_blank" href="{{ route('inventory.edithistory') }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v5h5"/><path d="M3.05 13A9 9 0 1 0 6 5.3L3 8"/><path d="M12 7v5l4 2"/></svg>ประวัติการแก้ไข</a>
    @endif
  </div>
</div>

<header class="hdr" id="hdr">
  <div class="hdr-in">
    <button class="hamburger" onclick="openSB()" aria-label="เมนูทั้งหมด" title="เมนูทั้งหมด"><span></span><span></span><span></span></button>
    <a class="nav-logo" href="{{ route('inventory.item') }}">
      <img src="https://lh3.googleusercontent.com/d/1qruaZSyb6gXrJ1Bc_l-p50LdZ6mszbE0" alt="">
      <span><b>3E TRADING</b><small>คลังสินค้าเครือ 3E</small></span>
    </a>
    <!-- หัวข้อหน้า + จำนวนรายการ (อยู่ก่อนเมนู) -->
    <div class="nav-title"><b id="pageTitle">ค้นหาสินค้า</b><span id="sumLine">กำลังโหลด...</span></div>
    <nav class="nav-links">
      <a class="on" id="navSearch" href="{{ route('inventory.item') }}" onclick="return goSearchView(event)">ค้นหาสินค้า</a>
      <a id="navTop" href="#top200" onclick="return goTopView(event)">Top 200 ขายดี</a>
      <a target="_blank" href="{{ route('inventory.transaction') }}">รายการสินค้า เข้า-ออก</a>
      <a target="_blank" href="{{ route('inventory.vehicles') }}">รถบริษัท</a>
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
  <div class="filters" role="search">
    <div class="fld"><label for="sId">รหัสสินค้า</label><input type="search" id="sId" placeholder="เช่น 3E-000" oninput="debounceFilter()" autocomplete="off" enterkeyhint="search"></div>
    <div class="fld"><label for="sName">ชื่อสินค้า <kbd class="kbd" title="กด / เพื่อค้นหา">/</kbd></label><input type="search" id="sName" placeholder="พิมพ์ชื่อสินค้า" oninput="debounceFilter()" autocomplete="off" enterkeyhint="search"><i class="mini-load" id="miniLoad" aria-label="กำลังโหลด"></i></div>
    <div class="fld seg-brand" id="brandSeg"><label for="sBrand">ยี่ห้อ</label>
      <div class="row-in"><input type="search" id="sBrand" placeholder="ทุกยี่ห้อ" oninput="debounceFilter();renderBrandDD()" autocomplete="off" enterkeyhint="search" role="combobox" aria-expanded="false" aria-controls="brandDD" aria-autocomplete="list"><svg class="chev" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></div>
      <div class="bdd" id="brandDD" role="listbox" hidden></div>
    </div>
    <div class="fld"><label for="sLoc">สถานที่เก็บ</label><input type="search" id="sLoc" placeholder="ชั้น / โกดัง" oninput="debounceFilter()" autocomplete="off" enterkeyhint="search"></div>
    <div class="fld fld-sel"><label for="sPriv">บริษัท</label><select id="sPriv" onchange="applyFilter()"><option value="">ทุกบริษัท</option><option value="3E">3E</option><option value="3IN">3IN</option><option value="3EM">3EM</option><option value="3EL">3EL</option><option value="HD">HD</option><option value="EP">EP</option><option value="3P">3P</option><option value="AE&T">AE&T</option></select><i></i></div>
    <div class="fld fld-sel"><label for="sType">ประเภท</label><select id="sType" onchange="applyFilter()"><option value="">ทุกประเภท</option><option value="คลัง">คลัง</option><option value="ทรัพย์สินบริษัท">ทรัพย์สินบริษัท</option></select><i></i></div>
    <div class="f-acts">
      <button class="btn btn-ghost" id="clrBtn" onclick="clearFilter()" hidden>ล้างตัวกรอง</button>
      @if(in_array($authRole, ['admin','user']))
        <button class="btn btn-solid" id="addBtn" onclick="addRow()"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>เพิ่มสินค้าใหม่</button>
      @endif
    </div>
  </div>

  <!-- Top 200 ขายดี: การ์ดเลื่อนแบบ Coverflow (แสดงเมื่อกดปุ่ม Top 200 ขายดี) -->
  <section class="top-wrap" id="topWrap" hidden aria-label="สินค้าขายดี">
    <div class="top-in">
      <span class="top-badge"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.2 6.3L20.5 10l-6.3 2.2L12 18.5l-2.2-6.3L3.5 10l6.3-1.7z"/><path d="M19 15l.9 2.6 2.6.9-2.6.9L19 22l-.9-2.6-2.6-.9 2.6-.9z" opacity=".6"/></svg>สินค้าขายดี</span>
      <span class="top-count" id="topCount"></span>
      <div class="top-head">
        <h2>Top 200 ขายดี</h2>
        <p id="topSub">สุ่มสินค้าขายดีมาแสดง</p>
      </div>
      <div class="top-empty" id="topEmpty" hidden><b>ไม่พบสินค้าขายดีที่ค้นหา</b><span>ลองพิมพ์ชื่อให้สั้นลง หรือกด “ล้างตัวกรอง”</span></div>
      <div class="swiper top-swiper" id="topSwiper">
        <div class="swiper-wrapper" id="topSlides"></div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev" aria-label="ก่อนหน้า"></div>
        <div class="swiper-button-next" aria-label="ถัดไป"></div>
      </div>
    </div>
  </section>
  <h3 class="top-list-h" id="topListH" hidden>รายการทั้งหมด 200 อันดับ</h3>
  <!-- แคตตาล็อกสินค้า (ใต้การ์ดเลื่อน ในเมนู Top 200 ขายดี) -->
  <section class="cat-wrap" id="catWrap" hidden aria-labelledby="catTitle">
    <div class="cat-head"><h3 id="catTitle">แคตตาล็อกสินค้า</h3><span id="catCount"></span></div>
    <div class="cat-grid" id="catGrid"></div>
  </section>
  <div class="tbl-wrap">
    <table id="dt">
      <colgroup><col style="width:175px"><col><col style="width:80px"><col style="width:120px"><col style="width:125px"><col style="width:105px"><col style="width:215px"><col style="width:110px">@if($authRole!=='viewer')<col style="width:145px">@endif</colgroup>
      <thead><tr><th>ID Item</th><th>ชื่อสินค้า</th><th>จำนวน</th><th>ยี่ห้อ</th><th>สถานที่เก็บ</th><th>ประเภท</th><th>บริษัท</th><th>หมวดหมู่</th>@if($authRole!=='viewer')<th>จัดการ</th>@endif</tr></thead>
      <tbody id="tb"></tbody>
    </table>
  </div>
  <div id="paging"></div>
</div>

<script>
const $=(id)=>document.getElementById(id);
const CSRF=document.querySelector('meta[name="csrf-token"]').content;
const ROLE=@json($authRole);
const UNAME=@json(strtolower(trim($authUser['name'] ?? '')));   // ชื่อผู้ใช้ (ใช้เช็คสิทธิ์แก้/ลบ)
const NEST_URL=@json($nestUrl);
const NEST_KEY=@json($nestKey);
// สร้าง: admin/user ; แก้ไข-ลบ: เฉพาะ admin หรือผู้ใช้ชื่อ chai
const CAN_ADD=(ROLE==='admin'||ROLE==='user'),CAN_EDIT=(ROLE==='admin'||UNAME==='ชัย');
const COLS=ROLE==='viewer'?8:9;
const COMPANIES=[{code:'3E',label:'Triple E Trading'},{code:'3IN',label:'Triple E Innovation'},{code:'3EM',label:'Triple E Empire Group'},{code:'3EL',label:'Triple E Lighting'},{code:'HD',label:'Hikari Denki'},{code:'EP',label:'Eita & Paul'},{code:'3P',label:'Triple P Factory & Eng'},{code:'AE&T',label:'AE&T International'}];
const PM={'3E':'b-3e','3IN':'b-3in','3EM':'b-3em','3EL':'b-3el','HD':'b-hd','EP':'b-ep','3P':'b-3p'};
const TM={'รับเข้าสต็อก':'t-in','คืนเข้าสต็อก':'t-ret','ขายสินค้าออก':'t-sell','ยืมสินค้า':'t-bor','เบิกของ':'t-wit'};
const API={
  async get(u){
    // เติม timestamp กัน browser/proxy cache ผลลัพธ์เดิม + no-store บังคับไม่เก็บ cache
    const sep = u.includes('?') ? '&' : '?';
    const noCacheUrl = u + sep + '_t=' + Date.now();
    return (await fetch(noCacheUrl, {
      cache: 'no-store',
      headers: {'Cache-Control':'no-cache, no-store, must-revalidate','Pragma':'no-cache'}
    })).json();
  },
  async post(u,d){
    return (await fetch(u,{
      method:'POST',
      cache:'no-store',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Cache-Control':'no-cache'},
      body:JSON.stringify(d)
    })).json();
  },
  async put(u,d){
    return (await fetch(u,{
      method:'PUT',
      cache:'no-store',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Cache-Control':'no-cache'},
      body:JSON.stringify(d)
    })).json();
  },
  async del(u){
    return (await fetch(u,{
      method:'DELETE',
      cache:'no-store',
      headers:{'X-CSRF-TOKEN':CSRF,'Cache-Control':'no-cache'}
    })).json();
  }
};

let uBrands=[],uLocs=[],products=[],subs={},filtered=[],pg=1,totalItems=0;
const PG=50;
let exMap={},openSubKey=null;
let filterTimeout=null;
let topMode=false;   // true = กำลังแสดงสินค้าขายดี (Top 200) เรียงตามจำนวนครั้งที่ขาย
let loadSeq=0;       // กันผลโหลดเก่า (async) มา render ทับผลใหม่ (เช่น กด Top200 ระหว่างโหลดปกติ)
let currentFilters={id:'',name:'',brand:'',location:'',priv:'',type:''};
let progressInterval = null;
let isEditingRow = false; // true ขณะกำลังเพิ่ม/แก้ไขแถว เพื่อกัน auto-refresh ทับข้อมูลที่กำลังพิมพ์

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
function toast(m,e){const t=$('toast');t.textContent=m;t.classList.toggle('err',!!e);t.classList.add('on');clearTimeout(t._t);t._t=setTimeout(()=>t.classList.remove('on'),2800)}
function ej(s){return(s||'').replace(/\\/g,'\\\\').replace(/'/g,"\\'").replace(/"/g,'&quot;').replace(/\n/g,'\\n')}
function eh(s){return(s||'').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;')}
function ci(s){return(s||'').replace(/[^a-zA-Z0-9]/g,'_')}
const ICN={
  chev:'<svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M7 4.5v15a1 1 0 0 0 1.53.85l12-7.5a1 1 0 0 0 0-1.7l-12-7.5A1 1 0 0 0 7 4.5z"/></svg>',
  plus:'<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>',
  chevL:'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>',
  chevR:'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>'
};
const fmtQty=q=>{const n=parseInt(q)||0;return `<span class="qty${n<=0?' zero':''}">${n.toLocaleString('en-US')}</span>`};
function tBadge(t){if(!t)return'<span class="badge b-klang">-</span>';return t==='คลัง'?'<span class="badge b-klang">คลัง</span>':t==='ทรัพย์สินบริษัท'?'<span class="badge b-asset">ทรัพย์สิน</span>':`<span class="badge b-klang">${t}</span>`}
function pBadge(p){if(!p)return'<span class="badge b-all">-</span>';const c=PM[p.trim()]||'b-all';const f=COMPANIES.find(x=>x.code===p.trim());return`<span class="badge ${c}">${f?f.code+' · '+f.label:p}</span>`}
const CO_CLS={'3E':'c-3e','3IN':'c-3in','3EM':'c-3em','3EL':'c-3el','HD':'c-hd','EP':'c-ep','3P':'c-3p','AE&T':'c-aet'};
// 3 ช่อง: ประเภท (สีเต็มช่อง) | บริษัท (สีเต็มช่อง) | หมวดหมู่
function infoCells(type,priv,cat){
  const t=(type||'').trim(),p=(priv||'').trim(),c=String(cat??'').trim();
  const tCell=t?`<td class="fill ${t==='ทรัพย์สินบริษัท'?'f-asset':'f-klang'}">${t==='ทรัพย์สินบริษัท'?'ทรัพย์สิน':eh(t)}</td>`:'<td class="c muted">-</td>';
  const co=COMPANIES.find(x=>x.code===p);
  const pCell=p?`<td class="co" title="${eh(co?co.label:p)}"><b>${eh(p)}</b>${co?' · '+eh(co.label):''}</td>`:'<td class="c muted">-</td>';
  return tCell+pCell+`<td class="cat">${c?eh(c):'-'}</td>`;
}
function cBadge(c){return c&&String(c).trim()?`<span class="badge b-all" title="หมวดหมู่">${eh(c)}</span>`:''}
function bldCat(v,id){return`<input type="text" id="${id}" value="${eh(v||'')}" placeholder="หมวดหมู่" class="finput">`}
function bldType(s,id){return`<select id="${id}" class="finput"><option value="คลัง" ${s==='คลัง'?'selected':''}>คลัง</option><option value="ทรัพย์สินบริษัท" ${s==='ทรัพย์สินบริษัท'?'selected':''}>ทรัพย์สินบริษัท</option></select>`}
function bldPriv(s,id){return`<select id="${id}" class="finput"><option value="" ${!s?'selected':''} disabled>-- บริษัท --</option>${COMPANIES.map(c=>`<option value="${c.code}" ${s===c.code?'selected':''}>${c.code} · ${c.label}</option>`).join('')}</select>`}

function debounceFilter(){
  clearTimeout(filterTimeout);
  filterTimeout=setTimeout(()=>{
    applyFilter();
  },400);
}

async function loadPage(page=1,showLoader=true){
  const seq=++loadSeq;
  if(showLoader) showOv(); else setMiniLoad(true);
  try{
    const params=new URLSearchParams({
      page:page,
      limit:PG,
      id:currentFilters.id||'',
      name:currentFilters.name||'',
      brand:currentFilters.brand||'',
      location:currentFilters.location||'',
      priv:currentFilters.priv||'',
      type:currentFilters.type||''
    });

    const res=await API.get('/api/items/paged?'+params.toString());
    if(seq!==loadSeq) return;   // มีการโหลดใหม่แทรกเข้ามา -> ทิ้งผลเก่า ไม่ render ทับ

    topMode=false;              // โหลดปกติ -> ออกจากโหมดขายดี
    setTop200BtnActive(false);
    products=res.data||[];
    subs=res.subs||{};
    totalItems=res.total||0;
    pg=res.page||1;
    collectOptions(res);

    render();
  }catch(e){
    console.error('Error loading data:',e);
    toast('โหลดข้อมูลล้มเหลว: '+e.message,true);
  }
  if(seq===loadSeq) setMiniLoad(false);
  if(showLoader) hideOv();
}

function applyFilter(){
  currentFilters={
    id:(document.getElementById('sId').value||'').trim(),
    name:(document.getElementById('sName').value||'').trim(),
    brand:(document.getElementById('sBrand').value||'').trim(),
    location:(document.getElementById('sLoc').value||'').trim(),
    priv:document.getElementById('sPriv').value,
    type:document.getElementById('sType').value
  };
  markSelects();
  if(topMode){ topApplyFilter(); return; } // อยู่ในโหมด Top 200: ค้นหาในสินค้าขายดี
  pg=1;
  loadPage(1, false);
}

function clearFilter(){
  ['sId','sName','sBrand','sLoc','sPriv','sType'].forEach(id=>document.getElementById(id).value='');
  syncSelects();
  markSelects();
  currentFilters={id:'',name:'',brand:'',location:'',priv:'',type:''};
  if(topMode){ topApplyFilter(); return; } // ล้างตัวกรองแต่ยังอยู่ในโหมด Top 200
  pg=1;
  loadPage(1,true);
}
// ออกจากโหมด Top 200 กลับไปตารางปกติ (คงคำค้นที่พิมพ์ไว้)
function exitTop200(){
  topMode=false;
  setTop200BtnActive(false);
  pg=1;
  loadPage(1,true);
}

// สลับเมนูที่เลือก (แถบบน + เมนูข้าง) ระหว่าง "ค้นหาสินค้า" กับ "Top 200 ขายดี"
function setTop200BtnActive(on){
  on=!!on;
  $('navSearch')?.classList.toggle('on',!on); $('navTop')?.classList.toggle('on',on);
  $('sbSearch')?.classList.toggle('cur',!on); $('sbTop')?.classList.toggle('cur',on);
  $('pageTitle').textContent=on?'Top 200 ขายดี':'ค้นหาสินค้า';
  document.title=(on?'Top 200 ขายดี':'ค้นหาสินค้า')+' - 3E TRADING';
  if($('addBtn')) $('addBtn').hidden=on;
  if(!on) hideTopCarousel();
}
// เมนู "Top 200 ขายดี": แสดงในหน้านี้ (ที่อยู่หน้าเว็บมี #top200 เปิดตรงๆ หรือรีเฟรชแล้วยังอยู่หน้าเดิม)
function goTopView(e){
  e&&e.preventDefault();
  closeSB();
  if(location.hash!=='#top200') history.replaceState(null,'',location.pathname+location.search+'#top200');
  if(!topMode) loadTop200();
  return false;
}
function goSearchView(e){
  e&&e.preventDefault();
  closeSB();
  if(location.hash) history.replaceState(null,'',location.pathname+location.search);
  if(topMode) exitTop200();
  return false;
}
window.addEventListener('hashchange',()=>{ if(location.hash==='#top200'){ if(!topMode) loadTop200(); } else if(topMode) exitTop200(); });

function toggleTop200(){
  if(topMode){ exitTop200(); return; }  // ออกจากโหมดขายดี -> กลับรายการปกติ
  loadTop200();
}

async function loadTop200(){
  const seq=++loadSeq;
  showOv('กำลังโหลดข้อมูล...');
  try{
    const res=await API.get('/api/items/top-selling?limit=200');
    if(seq!==loadSeq) return;   // มีการโหลดใหม่แทรกเข้ามา -> ทิ้งผลเก่า
    topAll=res.data||[];
    subs={};
    pg=1;
    topMode=true;
    applyFilterValues();
    // มีคำค้น: แสดงเฉพาะสินค้าขายดีที่ตรง/ใกล้เคียง เรียงตามความตรง / ไม่มีคำค้น: สุ่มลำดับ
    const topItems=topFilterList();
    products=topItems;totalItems=topItems.length;
    // โหลดรูป 20 ใบแรกให้เสร็จก่อน แล้วค่อยแสดงการ์ด (ตัวโหลดเต็มจอยังแสดงอยู่ระหว่างนี้)
    topReset(topItems);
    clearInterval(progressInterval); // แถบความคืบหน้าใช้ตัวเลขจริง (จำนวนรูปที่โหลดเสร็จ)
    await topPreloadFirst(topItems,(d,t)=>{
      if(seq!==loadSeq) return;
      const pc=Math.round(d/t*100);
      $('progressBar').style.width=pc+'%';$('ovPercent').textContent=pc+'%';
    });
    if(seq!==loadSeq) return;
    setTop200BtnActive(true);
    render();
    showTopCarousel(topItems);
    scrollTo({top:0,behavior:'smooth'});
  }catch(e){
    console.error('Error loading top-selling:',e);
    toast('โหลดสินค้าขายดีล้มเหลว: '+e.message,true);
  }
  hideOv();
}

/* ========== Top 200 ขายดี: การ์ดเลื่อนแบบ Coverflow (Swiper) ==========
   การโหลดรูป: กดเปิดแล้วโหลดรูปทุกรายการให้เสร็จในครั้งเดียว (มีตัวโหลดเต็มจอบอกความคืบหน้า)
   แล้วค่อยแสดงการ์ด รูปจึงขึ้นครบทุกใบ เลื่อนไปใบไหนก็ไม่ต้องรอ */
const TOP_BATCH=20;
let topSwiper=null;
let topBgToken=0;
const topImgCache=new Map(); // iditem → url รูป ('' = ไม่มีรูป)
// จำว่าสินค้าไหนใช้รูปอะไรไว้ในเบราว์เซอร์ 1 วัน เปิดครั้งถัดไปไม่ต้องถามเซิร์ฟเวอร์หารูปซ้ำ
const TOP_LS_KEY='3e-top-img',TOP_LS_TTL=24*60*60*1000;
let topLs={};
try{
  const o=JSON.parse(localStorage.getItem(TOP_LS_KEY)||'{}'),now=Date.now();
  for(const k in o){ if(o[k]&&now-o[k].t<TOP_LS_TTL) topLs[k]=o[k]; }
}catch(e){topLs={};}
let topLsTimer=null;
function topLsSave(){
  clearTimeout(topLsTimer);
  topLsTimer=setTimeout(()=>{try{localStorage.setItem(TOP_LS_KEY,JSON.stringify(topLs));}catch(e){}},800);
}
const topLoading=new Map();  // อันดับ(index) → Promise การโหลดรูป
const topReady=new Map();    // iditem → url ที่โหลดเสร็จแล้ว ('' = ไม่มีรูป/โหลดไม่ได้)
const topBatches=new Set();
// ลิงก์รูปจาก Google Drive → ขอจาก lh3 ตรงๆ ขนาดกว้าง 400px
// (เร็วกว่า drive.google.com/thumbnail ที่ต้อง redirect ก่อน 1 รอบ และไฟล์เล็กกว่ารูป 600px ประมาณครึ่ง)
const TOP_IMG_W=400;
function topDirectUrl(url){
  if(!url) return '';
  const m=url.match(/\/file\/d\/([a-zA-Z0-9_-]+)/)||url.match(/[?&]id=([a-zA-Z0-9_-]+)/)||url.match(/googleusercontent\.com\/d\/([a-zA-Z0-9_-]+)/);
  return m?`https://lh3.googleusercontent.com/d/${m[1]}=w${TOP_IMG_W}`:url;
}
const TOP_PH='<svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>';
function topMediaInner(i){
  const id=(window.__topItems||[])[i]?.iditem;
  if(topReady.has(id)){
    const src=topReady.get(id);
    return src?`<img class="ready" src="${eh(src)}" alt="" decoding="async">`:`<div class="tc-ph">${TOP_PH}<span>ยังไม่มีรูปสินค้า</span></div>`;
  }
  return `<div class="tc-ph loading">${TOP_PH}<span>กำลังโหลดรูป</span></div>`;
}
function topSlideHTML(it,i){
  const q=parseInt(it.quantity)||0;
  return `<div class="swiper-slide" data-i="${i}">
    <div class="tcard" role="group" aria-label="${eh(it.name)}">
      <div class="tc-media">${topMediaInner(i)}</div>
      <div class="tc-body">
        <div class="tc-name" title="${eh(it.name)}">${eh(it.name)}</div>
        <div class="tc-meta"><span>${eh(it.iditem)}${it.brand?' · '+eh(it.brand):''}</span><span>คงเหลือ <strong class="${q<=0?'zero':''}">${q.toLocaleString('en-US')}</strong></span></div>
      </div>
    </div>
  </div>`;
}
// การ์ดแคตตาล็อก (ใช้รูปชุดเดียวกับการ์ดเลื่อน)
function catCardHTML(it,i){
  const q=parseInt(it.quantity)||0;
  return `<article class="ccard" data-i="${i}">
    <div class="tc-media">${topMediaInner(i)}</div>
    <div class="tc-body">
      <div class="tc-name" title="${eh(it.name)}">${eh(it.name)}</div>
      <div class="tc-meta"><span>${eh(it.iditem)}${it.brand?' · '+eh(it.brand):''}</span><span>คงเหลือ <strong class="${q<=0?'zero':''}">${q.toLocaleString('en-US')}</strong></span></div>
    </div>
  </article>`;
}
// หารูปของสินค้า: ใช้รูปในข้อมูลสินค้าก่อน ถ้าไม่มีค่อยดูจากประวัติเข้า-ออก
async function topImageOf(it){
  if(topImgCache.has(it.iditem)) return topImgCache.get(it.iditem);
  let src=it.image||it.img||'';
  // เซิร์ฟเวอร์ส่งช่อง image มาแล้ว (รวมรูปจากประวัติเข้า-ออกให้แล้ว) → เชื่อค่านั้นเลย แม้จะว่าง ไม่ต้องถามเพิ่ม
  if(!src&&!('image' in it)){
    const c=topLs[it.iditem];
    if(c){ src=c.u; }            // เคยหาไว้แล้วภายใน 1 วัน: ใช้เลย ไม่ต้องถามเซิร์ฟเวอร์
    else{
      let ok=false;
      try{
        const rows=await API.get('/api/transaction/by-item/'+encodeURIComponent(it.iditem));
        src=(Array.isArray(rows)?rows:[]).map(r=>r['รูปประกอบ']).find(Boolean)||'';
        ok=true;
      }catch(e){}
      if(ok){ topLs[it.iditem]={u:src,t:Date.now()}; topLsSave(); } // จำไว้เฉพาะตอนถามสำเร็จ
    }
  }
  src=topDirectUrl(src);
  topImgCache.set(it.iditem,src);
  return src;
}
// ดาวน์โหลดรูปให้เสร็จ (เก็บใน cache ของเบราว์เซอร์) แล้วค่อยแสดง จะได้ขึ้นทันทีไม่กระพริบ
function topPreloadImg(src,ms=15000){
  return new Promise(res=>{
    if(!src){res(false);return;}
    const im=new Image();
    const t=setTimeout(()=>res(false),ms);
    im.onload=()=>{clearTimeout(t);res(true);};
    im.onerror=()=>{clearTimeout(t);res(false);};
    im.src=src;
  });
}
// ใส่รูปลงการ์ดอันดับ i ที่อยู่บนหน้า (ทุกสำเนาของสไลด์นั้น)
function topApply(i){
  const src=topReady.get((window.__topItems||[])[i]?.iditem);
  document.querySelectorAll(`#topSlides .swiper-slide[data-i="${i}"] .tc-media, #catGrid .ccard[data-i="${i}"] .tc-media`).forEach(m=>{
    if(m.querySelector('img')) return;
    const ph=m.querySelector('.tc-ph');
    if(!src){if(ph){ph.classList.remove('loading');ph.lastElementChild.textContent='ยังไม่มีรูปสินค้า';}return;}
    const img=new Image();img.alt='';img.decoding='async';
    img.onload=()=>{img.classList.add('ready');ph?.remove();};
    img.onerror=()=>{img.remove();if(ph){ph.classList.remove('loading');ph.lastElementChild.textContent='โหลดรูปไม่สำเร็จ';}};
    img.src=src;m.prepend(img);
  });
}
function topLoadIdx(i){
  const list=window.__topItems||[];
  if(i<0||i>=list.length) return Promise.resolve();
  if(topLoading.has(i)) return topLoading.get(i);
  const id=list[i].iditem;
  const p=(topReady.has(id)?Promise.resolve():topImageOf(list[i]).then(async src=>{
    const ok=await topPreloadImg(src);
    topReady.set(id,ok?src:'');
  })).then(()=>{
    topApply(i);
  });
  topLoading.set(i,p);
  return p;
}
function topLoadBatch(b){
  const n=(window.__topItems||[]).length;
  if(b<0||b*TOP_BATCH>=n) return Promise.resolve();
  topBatches.add(b);
  const jobs=[];
  for(let i=b*TOP_BATCH;i<Math.min(n,(b+1)*TOP_BATCH);i++) jobs.push(topLoadIdx(i));
  return Promise.all(jobs);
}
// โหลดรูปทุกการ์ดที่มองเห็นบนจออยู่ตอนนี้ (จอกว้างเห็นได้หลายใบ)
function topLoadVisible(){
  const wrap=$('topSwiper');
  if(!wrap||$('topWrap').hidden) return;
  const box=wrap.getBoundingClientRect(),pad=320;
  wrap.querySelectorAll('.swiper-slide').forEach(sl=>{
    const r=sl.getBoundingClientRect();
    if(r.width&&r.right>box.left-pad&&r.left<box.right+pad) topLoadIdx(+sl.dataset.i);
  });
}
function topLoadAround(center){
  const n=(window.__topItems||[]).length;
  if(!n) return;
  const b=Math.floor(center/TOP_BATCH);
  if(!topBatches.has(b)) topLoadBatch(b);
  if(center%TOP_BATCH>=TOP_BATCH-5&&!topBatches.has(b+1)) topLoadBatch(b+1);
  topLoadVisible();
}
/* ---------- ค้นหาในสินค้าขายดี: ตรงก่อน ถ้าไม่มีค่อยหาที่ใกล้เคียง ---------- */
let topAll=[];          // สินค้าขายดีทั้งหมดที่โหลดมา (ยังไม่กรอง)
let topMatchMode='';    // '' = ไม่มีคำค้น, 'match' = เจอที่ตรง, 'near' = เจอแต่ที่ใกล้เคียง, 'none' = ไม่เจอ
function applyFilterValues(){
  currentFilters={
    id:($('sId').value||'').trim(),name:($('sName').value||'').trim(),brand:($('sBrand').value||'').trim(),
    location:($('sLoc').value||'').trim(),priv:$('sPriv').value,type:$('sType').value
  };
}
const topNorm=v=>String(v??'').toLowerCase().replace(/[\s\-_/.,()"'#]+/g,'');
function topBigrams(t){const a=[];for(let i=0;i<t.length-1;i++)a.push(t.slice(i,i+2));return a;}
// ความคล้าย 0–1 (Dice coefficient จากตัวอักษรคู่ติดกัน) ใช้หาชื่อที่พิมพ์ผิดเล็กน้อย/เขียนต่างกัน
function topSimilar(a,b){
  if(!a||!b) return 0;
  if(a.length<2||b.length<2) return a===b?1:0;
  const A=topBigrams(a),B=topBigrams(b),m=new Map();
  B.forEach(x=>m.set(x,(m.get(x)||0)+1));
  let hit=0;A.forEach(x=>{const c=m.get(x);if(c){hit++;m.set(x,c-1);}});
  return 2*hit/(A.length+B.length);
}
// คะแนนความตรงของชื่อ: ตรงทั้งคำ > มีทุกคำที่พิมพ์ > มีบางคำ > คล้ายกัน
function topNameScore(it,q){
  const nq=topNorm(q),nn=topNorm(it.name),nid=topNorm(it.iditem);
  if(!nq) return 1;
  if(nn===nq) return 120;
  if(nn.startsWith(nq)) return 110;
  if(nn.includes(nq)||nid.includes(nq)) return 100;
  const words=q.toLowerCase().split(/\s+/).map(topNorm).filter(Boolean);
  if(words.length>1){
    const got=words.filter(w=>nn.includes(w)).length;
    if(got===words.length) return 90;
    if(got) return 40+30*got/words.length;
  }
  // ใกล้เคียง: เทียบกับทั้งชื่อ และกับช่วงของชื่อที่ยาวเท่าคำค้น (เผื่อพิมพ์แค่บางส่วน)
  let best=topSimilar(nq,nn);
  for(let i=0;i+nq.length<=nn.length;i+=Math.max(1,Math.floor(nq.length/3))) best=Math.max(best,topSimilar(nq,nn.slice(i,i+nq.length+1)));
  return best>=0.5?Math.round(best*35):0; // คล้ายกัน = ใกล้เคียง (คะแนนต่ำกว่า 40 เสมอ)
}
function topFilterList(){
  const f=currentFilters,has=v=>(v||'').trim();
  const inc=(v,q)=>topNorm(v).includes(topNorm(q));
  let list=topAll.filter(it=>
    (!has(f.id)||inc(it.iditem,f.id))&&
    (!has(f.brand)||inc(it.brand,f.brand))&&
    (!has(f.location)||inc(it.location,f.location))&&
    (!f.priv||(it.privilege||'').trim()===f.priv)&&
    (!f.type||(it.typeitem||'')===f.type));
  const q=has(f.name);
  if(!q){topMatchMode=hasFilter()?(list.length?'match':'none'):'';return hasFilter()?list:topShuffle(list);}
  const scored=list.map(it=>({it,sc:topNameScore(it,q)})).filter(x=>x.sc>0).sort((a,b)=>b.sc-a.sc);
  topMatchMode=!scored.length?'none':(scored[0].sc>=40?'match':'near');
  // ถ้ามีที่ตรง แสดงเฉพาะที่ตรง / ถ้าไม่มีเลย แสดงที่ใกล้เคียงที่สุด
  return (topMatchMode==='match'?scored.filter(x=>x.sc>=40):scored.slice(0,30)).map(x=>x.it);
}
// เปลี่ยนคำค้นตอนอยู่ในโหมด Top 200: กรองการ์ดใหม่ทันที (รูปที่เคยโหลดแล้วขึ้นทันที)
function topApplyFilter(){
  const list=topFilterList();
  products=list;totalItems=list.length;
  topReset(list);
  render();
  showTopCarousel(list);
  list.forEach((_,i)=>topLoadIdx(i)); // รูปโหลดไว้ครบแล้วตั้งแต่เปิด ขึ้นทันที
}
// สุ่มลำดับ (Fisher–Yates) ได้ชุดใหม่ทุกครั้งที่เปิด
function topShuffle(arr){
  const a=arr.slice();
  for(let i=a.length-1;i>0;i--){const j=Math.floor(Math.random()*(i+1));[a[i],a[j]]=[a[j],a[i]];}
  return a;
}
// เริ่มใหม่ทุกครั้งที่เปิด Top 200
function topReset(items){
  topBgToken++;
  window.__topItems=items;
  topLoading.clear();topBatches.clear();
}
// ขั้นที่ 1: โหลด 20 อันดับแรก (+ การ์ดท้ายแถวที่วนมาอยู่ซ้าย) ให้เสร็จก่อน พร้อมบอกความคืบหน้า
async function topPreloadFirst(items,onProgress){
  // โหลดรูป "ทั้งหมด" ให้เสร็จในครั้งเดียวก่อนแสดงการ์ด (ทีละ 12 รายการพร้อมกัน ไม่ให้เซิร์ฟเวอร์หนักเกินไป)
  const n=items.length;
  let done=0,next=0;
  onProgress&&onProgress(0,n);
  for(let b=0;b*TOP_BATCH<n;b++) topBatches.add(b);
  const worker=async()=>{
    while(next<n){
      const i=next++;
      await topLoadIdx(i);
      done++;onProgress&&onProgress(done,n);
    }
  };
  const all=Promise.all(Array.from({length:Math.min(12,n)},worker));
  await Promise.race([all,new Promise(r=>setTimeout(r,60000))]); // กันค้าง: รอไม่เกิน 60 วินาที แล้วแสดงเลย (ที่ค้างจะขึ้นตามทีหลัง)
}
// ขั้นที่ 2: โหลดที่เหลือเบื้องหลัง ทีละชุด ชุดละ 20 รูป
async function topBackgroundLoad(){
  const tok=topBgToken,n=(window.__topItems||[]).length;
  for(let b=1;b*TOP_BATCH<n;b++){
    if(tok!==topBgToken||!topMode) return;
    await topLoadBatch(b);
  }
}
function showTopCarousel(items){
  const q=(currentFilters.name||'').trim();
  $('topWrap').hidden=!topAll.length;
  $('topListH').hidden=true;
  document.querySelector('.tbl-wrap').hidden=!!topAll.length; // โหมด Top 200: แสดงเฉพาะการ์ด ไม่แสดงตาราง
  $('topSub').innerHTML=
    topMatchMode==='none'?`ไม่พบสินค้าขายดีที่ตรงหรือใกล้เคียงกับ “${eh(q||'ตัวกรอง')}”`:
    topMatchMode==='near'?`ไม่พบชื่อที่ตรงกับ “${eh(q)}” · แสดงสินค้าขายดีที่<b>ใกล้เคียง</b>`:
    topMatchMode==='match'?(q?`สินค้าขายดีที่ตรงกับ “${eh(q)}”`:'สินค้าขายดีตามตัวกรอง'):
    'สุ่มสินค้าขายดีมาแสดง';
  $('topEmpty').hidden=!!items.length;
  $('topSwiper').hidden=!items.length;
  $('topCount').innerHTML=`ทั้งหมด<b>${items.length}</b>รายการ`;
  if(topSwiper){topSwiper.destroy(true,true);topSwiper=null;}
  $('topSlides').innerHTML=items.map(topSlideHTML).join('');
  // แคตตาล็อกด้านล่าง: สินค้าชุดเดียวกับการ์ดเลื่อน (กรองตามคำค้นเหมือนกัน)
  $('catGrid').innerHTML=items.map(catCardHTML).join('');
  $('catCount').innerHTML=`ทั้งหมด <b>${items.length.toLocaleString('en-US')}</b> รายการ`;
  $('catWrap').hidden=!items.length;
  if(!items.length) return;
  const el=$('topSwiper');
  const start=()=>{
    if(!window.Swiper){ // โหลดไลบรารีไม่ได้: ใช้แบบเลื่อนแนวนอนธรรมดา
      el.classList.add('no-swiper');
      const w=el.querySelector('.swiper-wrapper');
      w.addEventListener('scroll',()=>{clearTimeout(w._t);w._t=setTimeout(topLoadVisible,80);},{passive:true});
      requestAnimationFrame(topLoadVisible);
      return;
    }
    el.classList.remove('no-swiper');
    topSwiper=new Swiper(el,{
      effect:'coverflow',grabCursor:true,centeredSlides:true,loop:items.length>4,slidesPerView:'auto',spaceBetween:50,
      coverflowEffect:{rotate:0,stretch:0,depth:100,modifier:2.5,slideShadows:false},
      autoplay:{delay:2500,disableOnInteraction:false,pauseOnMouseEnter:true},
      pagination:{el:el.querySelector('.swiper-pagination'),clickable:true,dynamicBullets:true},
      navigation:{nextEl:el.querySelector('.swiper-button-next'),prevEl:el.querySelector('.swiper-button-prev')},
      keyboard:{enabled:true},
      on:{init(s){requestAnimationFrame(topLoadVisible)},slideChange(s){topLoadAround(s.realIndex)},transitionEnd:topLoadVisible,resize:topLoadVisible}
    });
  };
  if(window.Swiper||document.readyState==='complete') start(); else window.addEventListener('load',start,{once:true});
}
function hideTopCarousel(){
  topBgToken++;
  if(topSwiper){topSwiper.destroy(true,true);topSwiper=null;}
  $('topWrap').hidden=true;$('topListH').hidden=true;$('topSlides').innerHTML='';
  $('catWrap').hidden=true;$('catGrid').innerHTML='';
  document.querySelector('.tbl-wrap').hidden=false;
}


function render(){
  const tb=$('tb');
  tb.innerHTML='';
  updateSummary();

  if(!products.length){
    const f=hasFilter();
    tb.innerHTML=`<tr class="empty-row"><td colspan="${COLS}"><b>${f?'ไม่พบสินค้าที่ตรงกับตัวกรอง':'ยังไม่มีสินค้าในระบบ'}</b><span>${f?'ลองพิมพ์ให้สั้นลง หรือกด “ล้างตัวกรอง”':'สินค้าที่เพิ่มในระบบจะแสดงที่นี่'}</span></td></tr>`;
    renderPg();
    return;
  }

  products.forEach((item,i)=>{
    const key=item._pid||item.iditem;
    const hasSub=!!(subs[key]?.length);
    const isExp=!!exMap[key];
    const isOpen=openSubKey===key;

    const tr=document.createElement('tr');
    tr.className='row'+(i%2?' alt':'')+(hasSub&&isExp&&!topMode?' exp':'');
    tr.dataset.pid=key;

    const expBtn=(hasSub&&!topMode)?`<button class="expand-btn${isExp?' open':''}" onclick="toggleExp('${ej(key)}',this)" title="แสดงรายการย่อย (${subs[key].length})" aria-label="แสดงรายการย่อย">${ICN.chev}</button>`:(topMode?'':`<span class="exp-gap"></span>`);
    const addSub=(CAN_ADD&&!topMode)?`<button class="add-sub-btn" onclick="toggleSubForm('${ej(key)}')" title="เพิ่มรายการย่อย" aria-label="เพิ่มรายการย่อย">${ICN.plus}</button>`:'';
    const rank=topMode?`<span class="rank">#${i+1}</span>`:'';
    const sellBadge=(topMode&&item.sell_count!=null)?`<span class="badge b-hd sell" title="จำนวนครั้งที่ขายออก">ขาย ${item.sell_count} ครั้ง</span>`:'';

    let h=`<td><div class="id-cell">${rank}${expBtn}${addSub}<strong>${eh(item.iditem)}</strong></div></td><td><button type="button" class="name-link" onclick="openTx('${ej(item.iditem)}','${ej(item.name)}')">${eh(item.name)}</button>${sellBadge}</td><td class="c">${fmtQty(item.quantity)}</td><td class="c">${eh(item.brand||'-')}</td><td class="c loc">${eh(item.location||'-')}</td>${infoCells(item.typeitem,item.privilege,item.category)}`;

    if(ROLE!=='viewer'){
      let btns='';
      if(CAN_EDIT&&!topMode) btns=`<button class="ib" onclick="editRow(${i})">แก้ไข</button><button class="ib a-del" onclick="delRow(${i})">ลบ</button>`;
      h+=`<td><div class="acts">${btns}</div></td>`;
    }

    tr.innerHTML=h;
    tb.appendChild(tr);

    if(CAN_ADD&&!topMode){
      const ftr=document.createElement('tr');
      ftr.className='sub-form-row'+(isOpen?'':' hide');
      ftr.dataset.sf=key;
      ftr.innerHTML=`<td colspan="${COLS}"><div class="sf"><span class="sf-id">รหัสใหม่: <b>${eh(key)}.<i>auto</i></b></span><input type="text" placeholder="ชื่อรุ่นย่อย" id="sn_${ci(key)}" class="finput" onkeydown="if(event.key==='Enter')saveSub('${ej(key)}')"><input type="text" id="sb_${ci(key)}" value="${eh(item.brand||'')}" placeholder="ยี่ห้อ" class="finput" style="max-width:160px" oninput="showAc(this,'brand')"><input type="text" id="sl_${ci(key)}" value="${eh(item.location||'')}" placeholder="สถานที่เก็บ" class="finput" oninput="showAc(this,'location')"><button class="ib a-save" onclick="saveSub('${ej(key)}')">บันทึก</button><button class="ib" onclick="cancelSub('${ej(key)}')">ยกเลิก</button></div></td>`;
      tb.appendChild(ftr);
    }

    if(hasSub){
      (subs[key]||[]).forEach((sub,si)=>{
        const str=document.createElement('tr');
        str.className='sub-row'+(isExp?'':' hide')+(si===subs[key].length-1?' last':'');
        str.dataset.so=key;
        str.dataset.sid=sub.iditem;

        let sh=`<td class="sub-id">${eh(sub.iditem)}</td><td><button type="button" class="name-link" onclick="openTx('${ej(sub.iditem)}','${ej(sub.name)}')">${eh(sub.name)}</button></td><td class="c">${fmtQty(sub.quantity)}</td><td class="c">${eh(sub.brand||'-')}</td><td class="c loc">${eh(sub.location||'-')}</td>${infoCells(sub.typeitem||item.typeitem,sub.privilege||item.privilege,sub.category||item.category)}`;

        if(ROLE!=='viewer'){
          let sbtns='';
          if(CAN_EDIT) sbtns=`<button class="ib" onclick="editSub('${ej(key)}',${si})">แก้ไข</button><button class="ib a-del" onclick="delSub('${ej(key)}',${si})">ลบ</button>`;
          sh+=`<td><div class="acts">${sbtns}</div></td>`;
        }

        str.innerHTML=sh;
        tb.appendChild(str);
      });
    }
  });

  renderPg();
}

function renderPg(){
  const el=$('paging');
  el.innerHTML='';
  if(topMode) return; // โหมดขายดีแสดงทั้งหมดในหน้าเดียว (จำนวนอยู่บนแถบบนแล้ว)
  const tot=Math.max(1,Math.ceil(totalItems/PG));
  if(tot<=1) return;
  const go=p=>{loadPage(p,true);scrollTo({top:0,behavior:'smooth'})};
  if(pg>1){const b=document.createElement('button');b.className='pg-btn';b.innerHTML=ICN.chevL+'ก่อนหน้า';b.onclick=()=>go(pg-1);el.appendChild(b)}
  const s=document.createElement('span');s.className='pg-info';s.innerHTML=`หน้า <b>${pg}</b> จาก ${tot}`;el.appendChild(s);
  if(pg<tot){const b=document.createElement('button');b.className='pg-btn';b.innerHTML='ถัดไป'+ICN.chevR;b.onclick=()=>go(pg+1);el.appendChild(b)}
}

function toggleExp(pid,btn){
  exMap[pid]=!exMap[pid];
  btn.classList.toggle('open',exMap[pid]);
  btn.closest('tr')?.classList.toggle('exp',!!exMap[pid]);
  document.querySelectorAll(`tr[data-so="${pid}"]`).forEach(r=>r.classList.toggle('hide',!exMap[pid]));
}

function toggleSubForm(pid){
  if(!CAN_ADD) return;
  if(openSubKey===pid){cancelSub(pid);return;}
  if(openSubKey) cancelSub(openSubKey);
  openSubKey=pid;
  isEditingRow=true;
  document.querySelector(`tr[data-sf="${pid}"]`)?.classList.remove('hide');
}


function showAc(inp,type){
  document.querySelectorAll('ul.ac').forEach(u=>u.remove());
  const list=type==='brand'?uBrands:uLocs,val=inp.value.toLowerCase();
  if(!val) return;
  const fil=list.filter(l=>l.toLowerCase().includes(val)).slice(0,10);
  if(!fil.length) return;
  const ul=document.createElement('ul');
  ul.className='ac';
  const rc=inp.getBoundingClientRect();
  ul.style.top=(rc.bottom+scrollY)+'px';
  ul.style.left=(rc.left+scrollX)+'px';
  ul.style.width=rc.width+'px';
  document.body.appendChild(ul);
  fil.forEach(v=>{
    const li=document.createElement('li');
    li.textContent=v;
    li.onclick=()=>{inp.value=v;ul.remove()};
    ul.appendChild(li);
  });
}

document.addEventListener('click',e=>{
  if(!e.target.closest('ul.ac')&&!e.target.matches('input'))
    document.querySelectorAll('ul.ac').forEach(u=>u.remove());
});

async function openTx(id,name){
  document.getElementById('txId').textContent=id;
  document.getElementById('txName').textContent=name;
  document.getElementById('txBody').innerHTML='<div class="tx-spin"><div class="tx-spin-inner"></div></div>';
  document.getElementById('txFoot').textContent='กำลังโหลด...';
  document.getElementById('txOv').classList.add('on');
  try{
    const rows=await API.get('/api/transaction/by-item/'+encodeURIComponent(id));
    rows.sort((a,b)=>{
      const p=ts=>{if(!ts) return 0;const m=ts.match(/^(\d{2})\/(\d{2})\/(\d{4})\s(\d{2}):(\d{2}):(\d{2})/);if(m) return new Date(m[3],m[2]-1,m[1],m[4],m[5],m[6]).getTime();return new Date(ts).getTime()||0};
      return p(b.Timestamp)-p(a.Timestamp);
    });
    renderTxModal(rows);
  }catch(e){
    document.getElementById('txBody').innerHTML=`<div class="tx-empty">โหลดประวัติไม่สำเร็จ ลองปิดแล้วเปิดใหม่อีกครั้ง</div>`;
    document.getElementById('txFoot').textContent='';
  }
}
function renderTxModal(data){
  const body=document.getElementById('txBody'),foot=document.getElementById('txFoot');
  if(!data.length){
    body.innerHTML='<div class="tx-empty">สินค้านี้ยังไม่มีประวัติเข้า-ออก</div>';
    foot.textContent='';
    return;
  }
  foot.textContent=`ทั้งหมด ${data.length} รายการ`;
  let h='<table class="tx-tbl"><thead><tr><th>วันที่</th><th>ผู้ดำเนินงาน</th><th>ประเภท</th><th>เอกสาร</th><th>รายการ</th><th class="num">จำนวน</th><th class="num">ราคา/หน่วย</th><th>ชั้นวาง</th><th>หมายเหตุ</th><th>รูป</th></tr></thead><tbody>';
  data.forEach(r=>{
    const tc=TM[r['ประเภทข้อมูล']||'']||'';
    const pic=r['รูปประกอบ']?`<a href="${eh(r['รูปประกอบ'])}" target="_blank" rel="noopener">ดูรูป</a>`:'-';
    const v=x=>(x===''||x==null)?'-':eh(String(x));
    h+=`<tr><td style="white-space:nowrap">${v(r.Timestamp)}</td><td>${v(r['ชื่อผู้ดำเนินงาน'])}</td><td class="tcell ${tc}">${v(r['ประเภทข้อมูล'])}</td><td>${v(r['หมายเลขเอกสาร'])}</td><td>${v(r['รายการ'])}</td><td class="num">${v(r['จำนวน'])}</td><td class="num">${ROLE==='viewer'?'-':v(r['ราคาต่อหน่วย'])}</td><td>${v(r['ชั้นวาง'])}</td><td>${v(r['หมายเหตุ'])}</td><td>${pic}</td></tr>`;
  });
  h+='</tbody></table>';
  body.innerHTML=h;
}

/* ========== ตัวช่วยเพิ่มเติม ========== */
function setMiniLoad(on){document.getElementById('miniLoad')?.classList.toggle('on',!!on)}
const FILTER_IDS=['sId','sName','sBrand','sLoc','sPriv','sType'];
function hasFilter(){return FILTER_IDS.some(id=>($(id).value||'').trim())}
function markSelects(){$('clrBtn').hidden=!hasFilter();}
// บรรทัดจำนวนรายการบนแถบบน
function updateSummary(){
  const n=totalItems.toLocaleString('en-US');
  $('sumLine').innerHTML=topMode?(hasFilter()?`พบสินค้าขายดี <b>${n}</b> รายการ`:`สินค้าขายดี <b>${n}</b> รายการ`):(hasFilter()?`พบ <b>${n}</b> รายการตามตัวกรอง`:`ทั้งหมด <b>${n}</b> รายการ`);
  markSelects();
}
// ส่วนหัวมีเงาเมื่อเลื่อนหน้าลง
window.addEventListener('scroll',()=>document.getElementById('hdr').classList.toggle('scrolled',scrollY>4),{passive:true});

// รวบรวมยี่ห้อ/สถานที่เก็บจากข้อมูลจริง (ใช้กับตัวกรองยี่ห้อ และช่องแนะนำตอนเพิ่ม/แก้ไข)
// เดิม uBrands/uLocs ไม่เคยถูกเติมค่า ช่องแนะนำจึงไม่เคยขึ้น
function addOptions(rows,brands,locs){
  const clean=v=>String((v&&typeof v==='object')?(v.brand??v.location??v.name??''):(v??'')).trim();
  const ok=v=>v&&v!=='-';
  const b=new Set(uBrands),l=new Set(uLocs);
  (brands||[]).map(clean).filter(ok).forEach(v=>b.add(v));
  (locs||[]).map(clean).filter(ok).forEach(v=>l.add(v));
  (rows||[]).forEach(x=>{if(!x) return;const bb=clean(x.brand),ll=clean(x.location);if(ok(bb))b.add(bb);if(ok(ll))l.add(ll);});
  uBrands=[...b].sort((a,c)=>a.localeCompare(c,'th'));
  uLocs=[...l].sort((a,c)=>a.localeCompare(c,'th'));
  if(!document.getElementById('brandDD').hidden) renderBrandDD();
}
function collectOptions(res){
  addOptions([...(res.data||[]),...Object.values(res.subs||{}).flat()],res.brands,res.locations);
}
// โหลดยี่ห้อ/สถานที่ของสินค้าทั้งหมดเบื้องหลังครั้งเดียว (หน้านี้โหลดทีละ 50 รายการ ตัวเลือกจะได้ครบ)
async function preloadAllOptions(){
  try{
    const url=p=>'/api/vehicles-items?'+new URLSearchParams({limit:200,page:p}).toString();
    const first=await API.get(url(1));
    const last=Math.max(1,parseInt(first.lastPage)||1);
    const take=r=>addOptions([...(r.data||[]),...Object.values(r.subs||{}).flat()],r.brands,r.locations);
    take(first);
    for(let p=2;p<=last;p++) take(await API.get(url(p)));
  }catch(e){console.warn('preload brand/location options failed',e);}
}

/* ========== ตัวกรองยี่ห้อแบบเด้งลง ========== */
function markMatch(text,q){
  if(!q) return eh(text);
  const i=text.toLowerCase().indexOf(q.toLowerCase());
  if(i<0) return eh(text);
  return eh(text.slice(0,i))+'<mark>'+eh(text.slice(i,i+q.length))+'</mark>'+eh(text.slice(i+q.length));
}
function renderBrandDD(){
  const dd=document.getElementById('brandDD');
  if(dd.hidden) return;
  const q=(document.getElementById('sBrand').value||'').trim();
  const all=uBrands;
  const exact=all.some(b=>b.toLowerCase()===q.toLowerCase());
  const list=(!q||exact)?all:all.filter(b=>b.toLowerCase().includes(q.toLowerCase()));
  let h=`<button type="button" class="bdd-opt bdd-all${!q?' on':''}" data-v="" role="option">ทุกยี่ห้อ</button><div class="bdd-sep"></div>`;
  h+=list.length
    ?list.map(b=>`<button type="button" class="bdd-opt${b.toLowerCase()===q.toLowerCase()?' on':''}" data-v="${eh(b)}" role="option"><span>${markMatch(b,exact?'':q)}</span></button>`).join('')
    :`<div class="bdd-empty">${all.length?'ไม่พบยี่ห้อที่ตรงกับ “'+eh(q)+'”':'กำลังโหลดรายการยี่ห้อ...'}</div>`;
  dd.innerHTML=h;
}
function openBrandDD(){
  const dd=document.getElementById('brandDD');
  if(!dd.hidden) return;
  dd.hidden=false;
  document.getElementById('brandSeg').classList.add('open');
  document.getElementById('sBrand').setAttribute('aria-expanded','true');
  renderBrandDD();
}
function closeBrandDD(){
  document.getElementById('brandDD').hidden=true;
  document.getElementById('brandSeg').classList.remove('open');
  document.getElementById('sBrand').setAttribute('aria-expanded','false');
}
function searchNow(){clearTimeout(filterTimeout);applyFilter();}
function chooseBrand(v){
  document.getElementById('sBrand').value=v;
  closeBrandDD();
  searchNow();
}
function moveBrandActive(step){
  const opts=[...document.getElementById('brandDD').querySelectorAll('.bdd-opt')];
  if(!opts.length) return;
  let i=opts.findIndex(o=>o.classList.contains('active'));
  opts[i]?.classList.remove('active');
  i=i<0?(step>0?0:opts.length-1):(i+step+opts.length)%opts.length;
  opts[i].classList.add('active');
  opts[i].scrollIntoView({block:'nearest'});
}
(function initBrandDD(){
  const seg=document.getElementById('brandSeg'),input=document.getElementById('sBrand'),dd=document.getElementById('brandDD');
  seg.addEventListener('mousedown',e=>{
    if(e.target.closest('.bdd')) return;
    if(e.target!==input){e.preventDefault();input.focus();}
    if(dd.hidden) openBrandDD(); else if(e.target!==input) closeBrandDD();
  });
  input.addEventListener('focus',openBrandDD);
  input.addEventListener('blur',()=>setTimeout(()=>{if(document.activeElement!==input) closeBrandDD();},120));
  dd.addEventListener('mousedown',e=>{
    e.preventDefault();
    const o=e.target.closest('.bdd-opt');
    if(o) chooseBrand(o.dataset.v||'');
  });
  input.addEventListener('keydown',e=>{
    if(e.key==='ArrowDown'){e.preventDefault();openBrandDD();moveBrandActive(1);}
    else if(e.key==='ArrowUp'){e.preventDefault();openBrandDD();moveBrandActive(-1);}
    else if(e.key==='Enter'){
      e.preventDefault();
      const a=dd.hidden?null:dd.querySelector('.bdd-opt.active');
      if(a) chooseBrand(a.dataset.v||''); else {closeBrandDD();searchNow();}
    }
    else if(e.key==='Escape'&&!dd.hidden){e.stopPropagation();closeBrandDD();}
  });
  // Enter ในช่องอื่น = ค้นหาทันที
  ['sName','sId','sLoc'].forEach(id=>document.getElementById(id).addEventListener('keydown',e=>{if(e.key==='Enter'){e.preventDefault();searchNow();}}));
})();

/* ========== คีย์ลัด: / ไปที่ช่องค้นหา, Esc ปิดหน้าต่างประวัติ ========== */
document.addEventListener('keydown',e=>{
  if(e.key==='Escape'){
    if(document.getElementById('txOv').classList.contains('on')) closeTx();
    return;
  }
  if(e.key==='/'&&!e.ctrlKey&&!e.metaKey&&!e.altKey){
    const tag=document.activeElement?.tagName||'';
    if(/^(INPUT|TEXTAREA|SELECT)$/.test(tag)||document.getElementById('txOv').classList.contains('on')) return;
    e.preventDefault();
    document.getElementById('sName').focus();
  }
});

/* ========== สลับธีมมืด/สว่าง (ใช้ค่าร่วมกับหน้าอื่น) ========== */
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
// บริษัท: ป้ายรหัสสี + ชื่อเต็ม / ประเภท: จุดสี
enhanceSelect($('sPriv'),v=>{
  if(!v) return `<span class="dd-ic">${DD_ALL_IC}</span><span class="dd-name">ทุกบริษัท</span>`;
  const c=COMPANIES.find(x=>x.code===v);
  return `<span class="dd-code ${PM[v]||'b-all'}">${eh(v)}</span><span class="dd-name">${eh(c?c.label:v)}</span>`;
});
enhanceSelect($('sType'),(v,t)=>{
  if(!v) return `<span class="dd-ic">${DD_ALL_IC}</span><span class="dd-name">ทุกประเภท</span>`;
  return `<span class="dd-dot ${v==='คลัง'?'k-klang':'k-asset'}"></span><span class="dd-name">${eh(t)}</span>`;
});

if(location.hash==='#top200') loadTop200(); else loadPage(1,true);
preloadAllOptions();

let itemsRefreshPending = false;
let itemsRefreshDebounce = null;

function isBusyEditingItems(){
  const txOpen = document.getElementById('txOv').classList.contains('on');
  return txOpen || isEditingRow || openSubKey !== null;
}

function requestItemsSilentRefresh(){
  clearTimeout(itemsRefreshDebounce);
  itemsRefreshDebounce = setTimeout(() => {
    if (topMode) return;   // โหมดขายดีเป็นมุมมองอิสระ ไม่ให้ refresh ทับ
    if (isBusyEditingItems()) { itemsRefreshPending = true; return; }
    loadPage(pg, false);
  }, 300);
}

function flushItemsPendingRefresh(){
  if (topMode) return;
  if (!itemsRefreshPending || isBusyEditingItems()) return;
  itemsRefreshPending = false;
  loadPage(pg, false);
}

function connectItemsSSE(){
  const url = `${NEST_URL}/items/events?key=${encodeURIComponent(NEST_KEY)}`;
  const es = new EventSource(url);
  es.onmessage = (e) => {
    if (e.data === 'heartbeat') return;
    if (e.data === 'items') requestItemsSilentRefresh();
  };
  es.onerror = () => console.warn('[SSE items] connection issue, browser will auto-retry');
}
connectItemsSSE();
function cancelSub(pid){
  document.querySelector(`tr[data-sf="${pid}"]`)?.classList.add('hide');
  if(openSubKey===pid) openSubKey=null;
  isEditingRow=false;
  flushItemsPendingRefresh();
}

function addRow(){
  if(!CAN_ADD) return;
  if(document.getElementById('nName')){
    alert('มีแถวเพิ่มสินค้าอยู่แล้ว');
    scrollTo(0,0);
    return;
  }
  isEditingRow=true;
  const tb=document.getElementById('tb'),tr=document.createElement('tr');
  tr.className='editing';
  tr.innerHTML=`<td><em class="auto">รหัสอัตโนมัติ</em></td><td><input type="text" id="nName" placeholder="ชื่อสินค้า" class="finput"></td><td><input type="number" id="nQty" value="0" readonly class="finput"></td><td><input type="text" id="nBrand" placeholder="ยี่ห้อ" class="finput" oninput="showAc(this,'brand')"></td><td><input type="text" id="nLoc" placeholder="สถานที่เก็บ" class="finput" oninput="showAc(this,'location')"></td><td>${bldType('คลัง','nType')}</td><td>${bldPriv('','nPriv')}</td><td>${bldCat('','nCat')}</td><td style="overflow:visible"><div class="acts"><button class="ib a-save" onclick="saveNew()">บันทึก</button><button class="ib" onclick="isEditingRow=false;loadPage(pg,false);flushItemsPendingRefresh()">ยกเลิก</button></div></td>`;
  tb.prepend(tr);
  scrollTo(0,0);
}

async function saveNew(){
  const nm=document.getElementById('nName').value.trim(),pr=document.getElementById('nPriv')?.value||'';
  if(!pr){alert('กรุณาเลือกบริษัท');return;}
  if(!nm){alert('กรุณากรอกชื่อ');return;}
  try{
    await API.post('/api/items',{name:nm,typeitem:document.getElementById('nType').value,location:document.getElementById('nLoc').value.trim(),brand:document.getElementById('nBrand').value.trim(),quantity:'0',privilege:pr,category:(document.getElementById('nCat').value||'').trim()});
    toast('เพิ่มสินค้าเรียบร้อย');
    isEditingRow=false;
    await loadPage(pg,false);
    flushItemsPendingRefresh();
  }catch(e){
    toast(e.message,true);
  }
}

async function saveSub(pid){
  if(!CAN_ADD) return;
  const cid=ci(pid),nm=(document.getElementById('sn_'+cid)?.value||'').trim();
  if(!nm){alert('กรุณากรอกชื่อ');return;}
  const par=products.find(p=>(p._pid||p.iditem)===pid);
  if(!par) return;
  try{
    await API.post('/api/items/sub',{parentId:pid,name:nm,brand:(document.getElementById('sb_'+cid)?.value||'').trim()||par.brand||'',location:(document.getElementById('sl_'+cid)?.value||'').trim()||par.location||'',typeitem:par.typeitem||'คลัง',quantity:'0',privilege:par.privilege||'',category:par.category||''});
    toast('เพิ่มรายการย่อยเรียบร้อย');
    openSubKey=null;
    isEditingRow=false;
    await loadPage(pg,false);
    flushItemsPendingRefresh();
  }catch(e){
    toast(e.message,true);
  }
}

function editRow(i){
  if(!CAN_EDIT) return;
  isEditingRow=true;
  const item=products[i],key=item._pid||item.iditem,row=document.querySelector(`tr[data-pid="${key}"]`);
  if(!row) return;
  const uid=ci(item.iditem);
  row.classList.add('editing');
  row.innerHTML=`<td><strong>${eh(item.iditem)}</strong></td><td><input type="text" id="eN_${uid}" value="${eh(item.name)}" class="finput"></td><td><input type="number" id="eQ_${uid}" value="${item.quantity}" class="finput"></td><td><input type="text" id="eB_${uid}" value="${eh(item.brand||'')}" class="finput" oninput="showAc(this,'brand')"></td><td><input type="text" id="eL_${uid}" value="${eh(item.location||'')}" class="finput" oninput="showAc(this,'location')"></td><td>${bldType(item.typeitem,'eT_'+uid)}</td><td>${bldPriv(item.privilege||'','eP_'+uid)}</td><td>${bldCat(item.category||'','eC_'+uid)}</td><td style="overflow:visible"><div class="acts"><button class="ib a-save" onclick="saveEdit(${i})">บันทึก</button><button class="ib" onclick="isEditingRow=false;render();flushItemsPendingRefresh()">ยกเลิก</button></div></td>`;
}

async function saveEdit(i){
  const item=products[i],uid=ci(item.iditem),nm=document.getElementById('eN_'+uid).value.trim(),pr=document.getElementById('eP_'+uid)?.value||'';
  if(!pr){alert('กรุณาเลือกบริษัท');return;}
  if(!nm){alert('กรุณากรอกชื่อ');return;}
  try{
    await API.put('/api/items/'+encodeURIComponent(item.iditem),{name:nm,quantity:document.getElementById('eQ_'+uid).value,typeitem:document.getElementById('eT_'+uid).value,location:document.getElementById('eL_'+uid).value.trim(),brand:document.getElementById('eB_'+uid).value.trim(),privilege:pr,category:(document.getElementById('eC_'+uid).value||'').trim()});
    toast('บันทึกสำเร็จ');
    isEditingRow=false;
    await loadPage(pg,false);
    flushItemsPendingRefresh();
  }catch(e){
    toast(e.message,true);
  }
}

async function delRow(i){
  if(!CAN_EDIT) return;
  const item=products[i];
  let cnt=0;
  try{cnt=(await API.get('/api/items/'+encodeURIComponent(item.iditem)+'/tx-count')).count||0}catch(e){}
  if(!confirm(cnt>0?`⚠️ ${item.iditem}\nมี Transaction ${cnt} รายการ\nดำเนินการต่อ?`:`ต้องการลบ ${item.iditem}?`)) return;
  const reason=(prompt('กรุณาระบุเหตุผลในการลบ '+item.iditem+' :','')||'').trim();
  if(!reason){ toast('ต้องระบุเหตุผลในการลบ',true); return; }
  try{
    const dr=await API.del('/api/items/'+encodeURIComponent(item.iditem)+'?reason='+encodeURIComponent(reason));
    if(dr&&dr.success===false){ toast(dr.error||'ลบไม่สำเร็จ',true); return; }
    toast('ลบเรียบร้อย');
    await loadPage(pg,false);
    flushItemsPendingRefresh();
  }catch(e){
    toast(e.message,true);
  }
}

function editSub(pid,si){
  if(!CAN_EDIT) return;
  isEditingRow=true;
  const sub=(subs[pid]||[])[si];
  if(!sub) return;
  const subTr=[...document.querySelectorAll(`tr[data-so="${pid}"]`)].find(r=>r.dataset.sid===sub.iditem);
  if(!subTr) return;
  const uid=ci(sub.iditem);
  subTr.classList.add('editing');
  subTr.innerHTML=`<td class="sub-id"><strong>${eh(sub.iditem)}</strong></td><td><input type="text" id="seN_${uid}" value="${eh(sub.name)}" class="finput"></td><td><input type="number" id="seQ_${uid}" value="${sub.quantity}" class="finput"></td><td><input type="text" id="seB_${uid}" value="${eh(sub.brand||'')}" class="finput" oninput="showAc(this,'brand')"></td><td><input type="text" id="seL_${uid}" value="${eh(sub.location||'')}" class="finput" oninput="showAc(this,'location')"></td><td>${bldType(sub.typeitem,'seT_'+uid)}</td><td>${bldPriv(sub.privilege||'','seP_'+uid)}</td><td>${bldCat(sub.category||'','seC_'+uid)}</td><td style="overflow:visible"><div class="acts"><button class="ib a-save" onclick="saveSubEdit('${ej(pid)}',${si})">บันทึก</button><button class="ib" onclick="isEditingRow=false;render();flushItemsPendingRefresh()">ยกเลิก</button></div></td>`;
}

async function saveSubEdit(pid,si){
  const sub=(subs[pid]||[])[si];
  if(!sub) return;
  const uid=ci(sub.iditem),nm=document.getElementById('seN_'+uid).value.trim();
  if(!nm){alert('กรุณากรอกชื่อ');return;}
  try{
    await API.put('/api/items/'+encodeURIComponent(sub.iditem),{name:nm,quantity:document.getElementById('seQ_'+uid).value,brand:document.getElementById('seB_'+uid).value.trim(),location:document.getElementById('seL_'+uid).value.trim(),typeitem:document.getElementById('seT_'+uid).value,privilege:document.getElementById('seP_'+uid)?.value||'',category:(document.getElementById('seC_'+uid)?.value||'').trim()});
    toast('บันทึกสำเร็จ');
    isEditingRow=false;
    await loadPage(pg,false);
    flushItemsPendingRefresh();
  }catch(e){
    toast(e.message,true);
  }
}

async function delSub(pid,si){
  if(!CAN_EDIT) return;
  const sub=(subs[pid]||[])[si];
  if(!sub||!confirm(`ลบ ${sub.iditem}?`)) return;
  const reason=(prompt('กรุณาระบุเหตุผลในการลบ '+sub.iditem+' :','')||'').trim();
  if(!reason){ toast('ต้องระบุเหตุผลในการลบ',true); return; }
  try{
    const dr=await API.del('/api/items/'+encodeURIComponent(sub.iditem)+'?reason='+encodeURIComponent(reason));
    if(dr&&dr.success===false){ toast(dr.error||'ลบไม่สำเร็จ',true); return; }
    toast('ลบเรียบร้อย');
    await loadPage(pg,false);
    flushItemsPendingRefresh();
  }catch(e){
    toast(e.message,true);
  }
}

function closeTx(){
  document.getElementById('txOv').classList.remove('on');
  flushItemsPendingRefresh();
}
</script>
</body>
</html>