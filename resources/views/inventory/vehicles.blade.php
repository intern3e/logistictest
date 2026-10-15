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
  <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* =====================================================================
       3E TRADING · ค้นหาสินค้า — "โชว์รูมกลางคืน"
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
    .img-btn{flex-shrink:0;width:42px;height:42px;border:1px solid var(--line-soft);border-radius:999px;background:var(--surface-2);color:var(--ink);cursor:pointer;display:grid;place-items:center;transition:background .2s,border-color .2s}
    .img-btn:hover{border-color:var(--line)}
    .img-btn:disabled{cursor:progress}
    .img-btn.spin svg{animation:imgspin .8s linear infinite}
    @keyframes imgspin{to{transform:rotate(360deg)}}
    @media (prefers-reduced-motion:reduce){.img-btn.spin svg{animation:none}}
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

    /* ===================== การ์ดสินค้า (พลิกได้) ===================== */
    .v-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:32px 24px}
    .v-card{position:relative;aspect-ratio:360/390;min-height:340px;perspective:1800px;outline:none;cursor:pointer}
    .v-card:focus-visible{outline:2px solid var(--accent);outline-offset:4px;border-radius:var(--r-card)}
    /* เปิดหน้าครั้งแรก: การ์ดไล่ขึ้นมาทีละใบ */
    .v-card.rise{animation:rise .8s var(--ease) both;animation-delay:calc(var(--i,0) * 55ms)}
    @keyframes rise{from{opacity:0;transform:translateY(22px)}}
    .v-inner{position:absolute;inset:0;transform-style:preserve-3d;-webkit-transform-style:preserve-3d;transition:transform .8s cubic-bezier(.45,.05,.55,.95)}
    .v-card:hover .v-inner,.v-card.flipped .v-inner,.v-card:has(:focus-visible) .v-inner,.v-card:focus-visible .v-inner{transform:rotateY(180deg)}
    @media(hover:none){.v-card:hover .v-inner{transform:none}.v-card.flipped .v-inner{transform:rotateY(180deg)}}

    .v-face{position:absolute;inset:0;display:flex;flex-direction:column;overflow:hidden;background:var(--surface);border-radius:var(--r-card);backface-visibility:hidden;-webkit-backface-visibility:hidden;box-shadow:0 0 0 1px var(--card-ring),var(--card-shadow);transition:box-shadow .4s var(--ease),visibility 0s linear .4s}
    .v-card:hover .v-face{box-shadow:var(--shadow-lift)}
    .v-front{transform:rotateY(0deg) translateZ(1px);visibility:visible}
    .v-back{transform:rotateY(180deg) translateZ(1px);visibility:hidden}
    .v-card:hover .v-front,.v-card.flipped .v-front,.v-card:has(:focus-visible) .v-front,.v-card:focus-visible .v-front{visibility:hidden}
    .v-card:hover .v-back,.v-card.flipped .v-back,.v-card:has(:focus-visible) .v-back,.v-card:focus-visible .v-back{visibility:visible}
    @media(hover:none){
      .v-card:hover .v-front{visibility:visible}.v-card:hover .v-back{visibility:hidden}
      .v-card.flipped .v-front{visibility:hidden}.v-card.flipped .v-back{visibility:visible}
    }
    /* ขอบเรืองแสงตามตำแหน่งเมาส์ (ทั้งการ์ดข้างเคียงด้วย) */
    .v-face::after{content:'';position:absolute;inset:0;z-index:4;border-radius:inherit;padding:1px;pointer-events:none;opacity:0;transition:opacity .35s;
      background:radial-gradient(240px circle at var(--mx,-999px) var(--my,-999px),var(--glow),transparent 72%);
      -webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude}
    .v-back::after{background:radial-gradient(240px circle at var(--mxr,-999px) var(--my,-999px),var(--glow),transparent 72%)}
    .v-grid.glow .v-face::after{opacity:1}
    /* แสงนวลบนใบสเปกตามเมาส์ */
    .v-back::before{content:'';position:absolute;inset:0;z-index:0;pointer-events:none;border-radius:inherit;background:radial-gradient(420px circle at var(--mxr,50%) var(--my,0),var(--accent-ring),transparent 60%);opacity:.0;transition:opacity .4s}
    :root[data-theme="dark"] .v-card:hover .v-back::before{opacity:.55}
    @media(prefers-reduced-motion:reduce){.v-inner,.v-face{transition:none}.vf-img img{transition:none}.v-card.rise{animation:none}}

    /* ---- ด้านหน้า ---- */
    .vf-media{position:relative;flex:1;min-height:0;background:var(--media)}
    .vf-img{position:absolute;inset:0;overflow:hidden}
    /* รูปจริงค่อย ๆ ชัดขึ้นจากภาพเบลอ (blur-up) */
    .vf-img img{display:block;width:100%;height:100%;object-fit:cover;opacity:0;filter:blur(18px) saturate(.85);transform:scale(1.08);transition:opacity .6s var(--ease),filter 1s var(--ease),transform 1.3s var(--ease)}
    .vf-img img.ready{opacity:1;filter:none;transform:scale(1)}
    .v-card.out .vf-img img.ready{filter:grayscale(.75) brightness(.9)}
    /* ระหว่างรอรูป: พื้นนุ่ม + รถกระบะวิ่ง (ล้อหมุน ตัวรถโยก ถนนเลื่อน) + แสงวิ่งผ่านแนวเฉียง */
    .vf-ph{position:relative;width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;padding-bottom:64px;overflow:hidden;color:var(--ph-ink);font-size:12px;font-weight:500;
      background:radial-gradient(120% 90% at 50% 30%,var(--ph-a),var(--ph-b))}
    .ph-car{width:128px;height:64px;margin-left:-14px;color:var(--ph-ink);opacity:.9;overflow:visible} /* ขยับซ้ายนิด ให้ตัวรถ+ไฟหน้าอยู่กลางกรอบ */
    .ph-car .wheel{transform-box:fill-box;transform-origin:center}
    .ph-car .lamp{fill:var(--accent);stroke:none}
    .ph-car .beam{fill:var(--accent);stroke:none;opacity:.22}
    .ph-car .road{stroke-dasharray:5 4}
    .vf-ph.loading .ph-car .body{animation:bob .5s ease-in-out infinite alternate}
    .vf-ph.loading .ph-car .wheel{animation:sp .6s linear infinite}
    .vf-ph.loading .ph-car .road{animation:road .45s linear infinite}
    .vf-ph.loading .ph-car .beam{animation:beam 1.2s ease-in-out infinite alternate}
    @keyframes bob{to{transform:translateY(-.7px)}}
    @keyframes road{to{stroke-dashoffset:9}}
    @keyframes beam{to{opacity:.38}}
    .vf-ph:not(.loading) .ph-car .lamp,.vf-ph:not(.loading) .ph-car .beam{display:none}
    .vf-ph.loading::before{content:'';position:absolute;left:50%;top:calc(50% - 32px);width:140px;height:140px;margin:-70px 0 0 -70px;border-radius:50%;background:radial-gradient(circle,var(--accent-ring),transparent 70%);animation:breathe 2.4s ease-in-out infinite}
    @keyframes breathe{50%{transform:scale(1.25);opacity:.45}}
    .vf-ph.loading::after{content:'';position:absolute;inset:-20%;background:linear-gradient(115deg,transparent 40%,rgba(255,255,255,.12) 50%,transparent 60%);transform:translateX(-60%);animation:shimmer 2s cubic-bezier(.4,0,.2,1) infinite}
    :root:not([data-theme="dark"]) .vf-ph.loading::after{background:linear-gradient(115deg,transparent 40%,rgba(255,255,255,.75) 50%,transparent 60%)}
    .vf-ph .ph-car,.vf-ph span{position:relative;z-index:1}
    .vf-ph:not(.loading) .ph-car{opacity:.5}
    .sr{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap}
    /* ตอนยังไม่มีรูป เงาใต้ชื่อเบาลง */
    .vf-media:has(.vf-ph) .vf-cap{padding-top:28px;background:linear-gradient(to top,rgba(17,19,21,.5),rgba(17,19,21,.12) 70%,transparent)}
    @keyframes shimmer{to{transform:translateX(60%)}}
    @media(prefers-reduced-motion:reduce){.vf-ph.loading::after,.vf-ph.loading::before,.vf-ph.loading .ph-car *{animation:none}}
    .vf-cap{position:absolute;left:0;right:0;bottom:0;padding:38px 20px 14px;color:#fff;pointer-events:none;background:linear-gradient(to top,var(--cap-a),var(--cap-b) 62%,transparent)}
    .vf-name{font-size:17px;font-weight:600;line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;text-shadow:0 1px 2px rgba(0,0,0,.3)}
    .vf-company-name{margin-top:2px;font-size:12.5px;color:rgba(255,255,255,.8);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .vf-flag{position:absolute;top:14px;left:14px;padding:4px 10px;border-radius:6px;background:rgba(255,255,255,.94);color:#B42318;font-size:12px;font-weight:600;box-shadow:0 4px 14px rgba(0,0,0,.18)}

    .vf-body{position:relative;flex:0 0 auto;padding:15px 20px 16px}
    .vf-head{display:flex;align-items:center;gap:12px;padding-bottom:13px;border-bottom:1px solid var(--line-soft)}
    .vf-head-txt{flex:1;min-width:0;display:flex;flex-direction:column;gap:1px}
    .vf-head-txt strong{font-size:14.5px;font-weight:600;letter-spacing:.02em;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .vf-head-txt span{font-size:12px;color:var(--mute)}
    .vf-head-txt .h-hover,.vf-head-txt .h-touch{display:none}
    @media(hover:none){.vf-head-txt .h-touch{display:inline}}
    .vf-co{flex-shrink:0;min-width:36px;height:36px;padding:0 6px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:11.5px;font-weight:700;letter-spacing:-.01em}
    .vf-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;padding-top:12px}
    .vf-stats>div{min-width:0;display:flex;flex-direction:column}
    .vf-stats>div:nth-child(2){align-items:center;text-align:center}
    .vf-stats>div:nth-child(3){align-items:flex-end;text-align:right}
    .vf-stats strong{max-width:100%;font-size:14px;font-weight:600;line-height:20px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .vf-stats strong.zero{color:var(--alert)}
    .vf-stats small{font-size:11.5px;line-height:17px;color:var(--mute)}

    /* ---- ด้านหลัง: ใบสเปกสินค้า ---- */
    .v-back{padding:20px 20px 18px;overflow:visible;transform-style:preserve-3d;-webkit-transform-style:preserve-3d}
    .v-back>*{position:relative;z-index:1}
    .vb-top{display:flex;align-items:center;justify-content:space-between;gap:10px}
    .vb-id{font-size:12.5px;font-weight:500;letter-spacing:.02em;color:var(--slate);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .vb-top .vf-co{min-width:30px;height:26px;border-radius:7px;font-size:11px}
    .vb-qty{margin-top:12px;transform:translateZ(40px)}
    .vb-qty small{display:block;font-size:12px;color:var(--slate)}
    .vb-qty strong{display:block;font-size:52px;font-weight:600;line-height:1.02;letter-spacing:-.03em}
    :root[data-theme="dark"] .vb-qty strong{background:linear-gradient(180deg,#FFFFFF 30%,#B9BBBE);-webkit-background-clip:text;background-clip:text;color:transparent;text-shadow:none}
    .vb-qty strong.zero,:root[data-theme="dark"] .vb-qty strong.zero{background:none;color:var(--alert);-webkit-text-fill-color:currentColor}
    .vb-spec{margin-top:12px;border-top:1px solid var(--line-soft)}
    .vb-spec div{display:flex;justify-content:space-between;gap:12px;padding:6px 0;border-bottom:1px solid var(--line-soft);font-size:13px}
    .vb-spec dt{flex-shrink:0;color:var(--slate)}
    .vb-spec dd{min-width:0;font-weight:600;text-align:right;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .vb-note{margin-top:10px;font-size:12.5px;line-height:1.55;color:var(--ink-2);display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
    .vb-cta{margin-top:auto;display:flex;align-items:center;justify-content:center;gap:8px;width:100%;height:42px;border:none;border-radius:999px;background:var(--btn);color:var(--btn-ink);font-size:13.5px;font-weight:600;cursor:pointer;transform:translateZ(30px);transition:background .2s,transform .25s var(--ease),box-shadow .25s}
    .vb-cta:hover{background:var(--btn-hover);transform:translateZ(30px) scale(1.015);box-shadow:0 10px 30px -10px var(--accent-ring)}
    .vb-cta:active{transform:translateZ(30px) scale(.98)}
    .vb-cta svg{transition:transform .25s var(--ease)}
    .vb-cta:hover svg{transform:translateX(3px)}

    /* สีป้ายบริษัท */
    .b-3e{background:#E8EEF8;color:#1F4E9C}
    .b-3in{background:#E3F1EA;color:#1E6B4A}
    .b-3em{background:#EEEAF6;color:#5B3E96}
    .b-3el{background:#FAEFD9;color:#8A5A06}
    .b-hd{background:#F8E6E3;color:#A3322A}
    .b-ep{background:#F8E7EE;color:#9C2F5E}
    .b-3p{background:#E8F1E0;color:#4B6B22}
    .b-all{background:#ECEDEA;color:#4A4E52}
    :root[data-theme="dark"] .b-3e{background:rgba(96,148,255,.14);color:#9DBDFF}
    :root[data-theme="dark"] .b-3in{background:rgba(64,196,140,.13);color:#7FDDB3}
    :root[data-theme="dark"] .b-3em{background:rgba(160,124,255,.15);color:#C3AEFF}
    :root[data-theme="dark"] .b-3el{background:rgba(242,176,46,.14);color:#F6C46B}
    :root[data-theme="dark"] .b-hd{background:rgba(255,110,96,.14);color:#FF9F94}
    :root[data-theme="dark"] .b-ep{background:rgba(255,110,170,.13);color:#FF9DC6}
    :root[data-theme="dark"] .b-3p{background:rgba(150,210,80,.13);color:#BEE58C}
    :root[data-theme="dark"] .b-all{background:rgba(255,255,255,.08);color:#C8C9CA}

    /* ===================== ไม่พบข้อมูล ===================== */
    .empty{grid-column:1/-1;padding:80px 16px;text-align:center}
    .empty h3{font-size:19px;font-weight:600}
    .empty p{margin-top:6px;color:var(--slate)}
    .empty button{margin-top:20px;height:44px;padding:0 22px;border:none;border-radius:999px;background:var(--btn);color:var(--btn-ink);font-weight:600;cursor:pointer}

    /* ===================== เลขหน้า ===================== */
    #paging{display:flex;justify-content:center;align-items:center;gap:10px;padding-top:44px}
    .pg-btn{display:inline-flex;align-items:center;gap:6px;height:44px;padding:0 20px;border:1px solid var(--line);border-radius:999px;background:var(--surface);color:var(--ink);font-size:14px;font-weight:600;cursor:pointer;transition:border-color .2s,box-shadow .2s}
    .pg-btn:hover{border-color:var(--accent);box-shadow:0 0 0 4px var(--accent-ring)}
    .pg-info{padding:0 14px;font-size:14px;color:var(--slate)}
    .pg-info b{color:var(--ink);font-weight:600}

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

<div class="tx-ov" id="txOv" onclick="if(event.target===this)closeTx()">
  <div class="tx-modal" role="dialog" aria-modal="true" aria-labelledby="txTitle">
    <div class="tx-head">
      <div class="tx-head-t"><h3 id="txTitle">ประวัติเข้า-ออก</h3><span class="tx-badge" id="txId">-</span><span id="txName"></span></div>
      <button class="tx-xbtn" onclick="closeTx()" aria-label="ปิด"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div class="tx-body" id="txBody"><div class="tx-spin"><div class="tx-spin-inner"></div></div></div>
    <div class="tx-foot" id="txFoot">กำลังโหลด...</div>
  </div>
</div>


<div class="sb-ov" id="sbOv" onclick="closeSB()"></div>
<div class="sidebar" id="sidebar">
 <div class="sb-head"><img src="https://lh3.googleusercontent.com/d/1qruaZSyb6gXrJ1Bc_l-p50LdZ6mszbE0" alt=""><span>3E TRADING</span><button class="sb-close" onclick="closeSB()" aria-label="ปิดเมนู">&#10005;</button></div>
  <div class="sb-nav">
    <div class="sb-sec">เมนู</div>
    <a class="sb-item" target="_blank" href="{{ route('inventory.transaction', ['create_by' => $authUser['name'] ?? '']) }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>รายการสินค้า เข้า-ออก</a>
    <a class="sb-item cur" target="_blank" href="{{ route('inventory.item', ['create_by' => $authUser['name'] ?? '']) }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>ค้นหาสินค้า</a>
  </div>
</div>

<header class="hdr" id="hdr">
  <div class="hdr-in">
    <a class="nav-logo" href="{{ route('inventory.item', ['create_by' => $authUser['name'] ?? '']) }}">
      <img src="https://lh3.googleusercontent.com/d/1qruaZSyb6gXrJ1Bc_l-p50LdZ6mszbE0" alt="">
      <span><b>3E TRADING</b><small>คลังสินค้าเครือ 3E</small></span>
    </a>
    <nav class="nav-links">
      <a class="on" href="{{ route('inventory.item', ['create_by' => $authUser['name'] ?? '']) }}">ค้นหาสินค้า</a>
      <a target="_blank" href="{{ route('inventory.transaction', ['create_by' => $authUser['name'] ?? '']) }}">รายการสินค้า เข้า-ออก</a>
    </nav>

    <div class="sbar" role="search">
      <label class="seg seg-name">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="search" id="sName" placeholder="ค้นหาชื่อสินค้า" aria-label="ชื่อสินค้า" oninput="debounceFilter()" autocomplete="off" enterkeyhint="search">
        <kbd title="กด / เพื่อค้นหา">/</kbd>
      </label>
      <div class="seg seg-brand" id="brandSeg">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
        <input type="search" id="sBrand" placeholder="ทุกยี่ห้อ" aria-label="ยี่ห้อ" oninput="debounceFilter();renderBrandDD()" autocomplete="off" enterkeyhint="search" role="combobox" aria-expanded="false" aria-controls="brandDD" aria-autocomplete="list">
        <svg class="chev" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        <div class="bdd" id="brandDD" role="listbox" hidden></div>
      </div>
      <i class="mini-load" id="miniLoad" aria-label="กำลังโหลด"></i>
      <button type="button" class="sbar-x" id="sbarX" onclick="clearFilter()" hidden>ล้างการค้นหา</button>
    </div>

    <div class="nav-right">
      <button type="button" class="img-btn" id="imgBtn" onclick="reloadImages()" aria-label="โหลดรูปใหม่ทั้งหมด" title="โหลดรูปใหม่ (ใช้เมื่อเปลี่ยนรูปแล้วรูปไม่เปลี่ยน)"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-2.64-6.36"/><polyline points="21 3 21 9 15 9"/></svg></button>
      <button type="button" class="theme-btn" id="themeBtn" onclick="toggleTheme(event)" aria-label="สลับโหมดสว่าง/มืด" title="สลับโหมดสว่าง/มืด"><svg class="i-sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="4.2"/><path d="M12 2.5v2.2M12 19.3v2.2M4.6 4.6l1.6 1.6M17.8 17.8l1.6 1.6M2.5 12h2.2M19.3 12h2.2M4.6 19.4l1.6-1.6M17.8 6.2l1.6-1.6"/></svg><svg class="i-moon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.5 14.5A8.5 8.5 0 0 1 9.5 3.5a8.5 8.5 0 1 0 11 11z"/></svg></button>
      <span class="nav-user" title="{{ $authUser['name'] ?? '' }} · {{ strtoupper($authRole) }}">
        <span class="nav-user-ic"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
        <span class="nav-user-name">{{ $authUser['name'] ?? '' }}</span>
      </span>
      <a href="http://server_update:8000/solist" class="nav-btn">หน้าหลัก</a>
      <button class="hamburger" onclick="openSB()" aria-label="เปิดเมนู"><span></span><span></span><span></span></button>
    </div>
  </div>
</header>

<div id="content">
  <div class="v-grid" id="tb"></div>
  <div id="paging"></div>
</div>

<script>
const CSRF=document.querySelector('meta[name="csrf-token"]').content;
const ROLE=@json($authRole);
const NEST_URL=@json($nestUrl);
const NEST_KEY=@json($nestKey);
const COMPANIES=[{code:'3E',label:'Triple E Trading',short:'Trading'},{code:'3IN',label:'Triple E Innovation',short:'Innovation'},{code:'3EM',label:'Triple E Empire Group',short:'Empire Group'},{code:'3EL',label:'Triple E Lighting',short:'Lighting'},{code:'HD',label:'Hikari Denki',short:'Hikari Denki'},{code:'EP',label:'Eita & Paul',short:'Eita & Paul'},{code:'3P',label:'Triple P Factory & Eng',short:'Triple P'},{code:'AE&T',label:'AE&T International',short:'AE&T Intl.'}];
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
  chev:(s)=>svgI('<polyline points="9 18 15 12 9 6"/>',s),
  chevL:(s)=>svgI('<polyline points="15 18 9 12 15 6"/>',s),
  image:(s)=>svgI('<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>',s)
};

// เรียก API แล้ว "โยน error" เมื่อ server ตอบไม่สำเร็จ
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
const API={get:(u)=>apiRequest('GET',u)};

const HIDE_IDITEMS=['3E-000013']; // ID ที่แสดงซ้อน/ซ้ำ ไม่ต้องแสดง (รายการอื่นแสดงตามปกติ)
let uBrands=[],products=[],subs={},pg=1,totalItems=0;
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
// แถบโหลดตอนดึงรายการสินค้า (ขยับเองไม่เกิน 90% แล้วเต็มเมื่อโหลดเสร็จ)
function startProgressSimulation(){
  ovProg=0;setOvBar(0);
  clearInterval(progressInterval);
  progressInterval=setInterval(()=>{
    ovProg+=ovProg<25?Math.random()*8:Math.random()*1.5;
    if(ovProg>90) ovProg=90;
    setOvBar(ovProg);
  },150);
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
function toast(m,e){const t=$('toast');t.textContent=m;t.classList.toggle('err',!!e);t.classList.add('on');clearTimeout(t._t);t._t=setTimeout(()=>t.classList.remove('on'),3200)}
function ej(s){return(s||'').replace(/\\/g,'\\\\').replace(/'/g,"\\'").replace(/"/g,'&quot;').replace(/\n/g,'\\n')}
function eh(s){return String(s??'').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;')}
const sleep=(ms)=>new Promise(r=>setTimeout(r,ms));

function debounceFilter(){
  updateClearBtn();
  clearTimeout(filterTimeout);
  filterTimeout=setTimeout(applyFilter,400);
}

// ปุ่ม "ล้าง" ในแถบค้นหา แสดงเฉพาะตอนมีตัวกรอง
function updateClearBtn(){
  $('sbarX').hidden=!(($('sName').value||'').trim()||($('sBrand').value||'').trim());
}
// กดปุ่มค้นหาวงกลม หรือ Enter = ค้นหาทันทีไม่ต้องรอ
function searchNow(){
  clearTimeout(filterTimeout);
  applyFilter();
}
$('sName').addEventListener('keydown',e=>{if(e.key==='Enter'){e.preventDefault();searchNow();}});
// ส่วนหัวมีเงาเมื่อเลื่อนหน้าลง
window.addEventListener('scroll',()=>$('hdr').classList.toggle('scrolled',scrollY>4),{passive:true});

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
  return {data,subs:first.subs||{},brands:first.brands||[]};
}

function collectBrands(serverBrands,keepOld){
  const name=b=>String((b&&typeof b==='object')?(b.brand??b.name??''):(b??'')).trim();
  const fromItems=[...allProducts,...Object.values(allSubs||{}).flat()].map(x=>x&&x.brand);
  const found=[...(Array.isArray(serverBrands)?serverBrands:[]),...fromItems].map(name).filter(b=>b&&b!=='-');
  uBrands=[...new Set([...(keepOld?uBrands:[]),...found])];
  fillDatalists();
}
// ===== รายการยี่ห้อแบบเด้งลง: คลิกช่องยี่ห้อแล้วขึ้นทันที / พิมพ์เพื่อหา / เลือกแล้วกรองทันที =====
function fillDatalists(){ if(!$('brandDD').hidden) renderBrandDD(); }
function brandList(){
  return [...new Set(uBrands.map(b=>String(b??'').trim()).filter(Boolean))].sort((a,b)=>a.localeCompare(b,'th'));
}
function markMatch(text,q){
  if(!q) return eh(text);
  const i=text.toLowerCase().indexOf(q.toLowerCase());
  if(i<0) return eh(text);
  return eh(text.slice(0,i))+'<mark>'+eh(text.slice(i,i+q.length))+'</mark>'+eh(text.slice(i+q.length));
}
function renderBrandDD(){
  const dd=$('brandDD');
  if(dd.hidden) return;
  const q=($('sBrand').value||'').trim();
  const all=brandList();
  // ถ้าค่าในช่องตรงกับยี่ห้อที่มีพอดี (เพิ่งเลือกมา) ให้แสดงทั้งหมด จะได้เปลี่ยนยี่ห้อง่าย
  const exact=all.some(b=>b.toLowerCase()===q.toLowerCase());
  const list=(!q||exact)?all:all.filter(b=>b.toLowerCase().includes(q.toLowerCase()));
  let h=`<button type="button" class="bdd-opt bdd-all${!q?' on':''}" data-v="" role="option">ทุกยี่ห้อ</button><div class="bdd-sep"></div>`;
  h+=list.length
    ?list.map(b=>`<button type="button" class="bdd-opt${b.toLowerCase()===q.toLowerCase()?' on':''}" data-v="${eh(b)}" role="option"><span>${markMatch(b,exact?'':q)}</span></button>`).join('')
    :`<div class="bdd-empty">${all.length?'ไม่พบยี่ห้อที่ตรงกับ “'+eh(q)+'”':'ยังไม่มีข้อมูลยี่ห้อ'}</div>`;
  dd.innerHTML=h;
}
function openBrandDD(){
  const dd=$('brandDD');
  if(!dd.hidden) return;
  dd.hidden=false;
  $('brandSeg').classList.add('open');
  $('sBrand').setAttribute('aria-expanded','true');
  renderBrandDD();
}
function closeBrandDD(){
  $('brandDD').hidden=true;
  $('brandSeg').classList.remove('open');
  $('sBrand').setAttribute('aria-expanded','false');
}
function chooseBrand(v){
  $('sBrand').value=v;
  closeBrandDD();
  searchNow();
}
function moveBrandActive(step){
  const opts=[...$('brandDD').querySelectorAll('.bdd-opt')];
  if(!opts.length) return;
  let i=opts.findIndex(o=>o.classList.contains('active'));
  opts[i]?.classList.remove('active');
  i=i<0?(step>0?0:opts.length-1):(i+step+opts.length)%opts.length;
  opts[i].classList.add('active');
  opts[i].scrollIntoView({block:'nearest'});
}
(function initBrandDD(){
  const seg=$('brandSeg'),input=$('sBrand'),dd=$('brandDD');
  seg.addEventListener('mousedown',e=>{
    if(e.target.closest('.bdd')) return;
    if(e.target!==input){e.preventDefault();input.focus();}
    if(dd.hidden) openBrandDD(); else if(e.target!==input) closeBrandDD();
  });
  input.addEventListener('focus',openBrandDD);
  input.addEventListener('blur',()=>setTimeout(()=>{if(document.activeElement!==input) closeBrandDD();},120));
  // ใช้ mousedown + preventDefault เพื่อไม่ให้ช่องหลุดโฟกัสก่อนเลือก
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
})();

// โหลดข้อมูลทั้งหมด (ตามตัวกรองปัจจุบัน) แล้วแบ่งหน้าแสดงผลฝั่ง client
// keepPage=true ใช้ตอน refresh เงียบๆ (SSE) ให้อยู่หน้าเดิมที่ผู้ใช้ดูอยู่
async function fetchAllAndRender(showLoader=true,keepPage=false){
  const seq=++renderSeq;
  if(showLoader) showOv(); else setMiniLoad(true);
  try{
    const r=await fetchItemsAllPages();
    if(seq!==renderSeq) return;
    allProducts=r.data;
    allSubs=r.subs;
    totalItems=allProducts.length;
    // รายการยี่ห้อ: รวมจากที่ server ส่งมา (ถ้ามี) + ยี่ห้อของสินค้าทุกชิ้นที่โหลดมา
    // ตอนกำลังกรองอยู่ ข้อมูลมีแค่บางส่วน จึงรวมกับรายการเดิมไว้ ไม่ให้ตัวเลือกหายไป
    collectBrands(r.brands,!!(currentFilters.brand||currentFilters.name));
    const maxPg=Math.max(1,Math.ceil(totalItems/PG));
    await renderPage(keepPage?Math.min(pg,maxPg):1,seq);
  }catch(e){
    if(seq===renderSeq){console.error('Error loading data:',e);toast('โหลดข้อมูลไม่สำเร็จ: '+e.message,true);}
  }finally{
    if(seq===renderSeq){if(ovIsOn()) hideOv();setMiniLoad(false);}
  }
}

// เปลี่ยนหน้า: แสดงการ์ดหน้าใหม่ทันที รูปจะทยอยตามมา
async function goPage(p){
  const seq=++renderSeq;
  const ok=await renderPage(p,seq);
  if(ok) scrollTo({top:0,behavior:'smooth'});
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
    priv:'',
    type:''
  };
  updateClearBtn();
  fetchAllAndRender(false);
}

function clearFilter(){
  ['sName','sBrand'].forEach(id=>$(id).value='');
  updateClearBtn();
  currentFilters={name:'',brand:'',location:'',priv:'',type:''};
  fetchAllAndRender(true);
}

/* ========== การ์ด ========== */
// สร้างการ์ดแบบพลิก: ด้านหน้า = รูปเต็มขอบพร้อมชื่อ/บริษัท + รหัส/ป้ายบริษัท + คงเหลือ ยี่ห้อ ประเภท
// ด้านหลัง = ใบสเปก: จำนวนคงเหลือตัวใหญ่ + ยี่ห้อ/ประเภท/บริษัท + หมายเหตุ + ปุ่มดูประวัติเข้า-ออก
function buildCard(it,parent,isSub){
  const card=document.createElement('div');
  card.className='v-card'+(isSub?' sub':'')+((parseInt(it.quantity)||0)<=0?' out':'');
  card.tabIndex=0;
  card.setAttribute('aria-label',(it.name||'')+' รหัส '+(it.iditem||''));

  const type=it.typeitem||parent?.typeitem||'';
  const priv=(it.privilege||parent?.privilege||'').trim();
  const comp=COMPANIES.find(c=>c.code===priv);
  const qty=parseInt(it.quantity)||0;
  const brand=it.brand||parent?.brand||'';
  const typeLabel=type==='ทรัพย์สินบริษัท'?'ทรัพย์สิน':(type||'-');
  const idJs=ej(it.iditem);

  const coName=comp?comp.label:(priv||'ไม่ระบุบริษัท');
  const coChip=priv?`<span class="vf-co ${PM[priv]||'b-all'}" title="${eh(coName)}">${eh(priv)}</span>`:'';
  card.innerHTML=`
    <div class="v-inner">
      <div class="v-face v-front">
        <div class="vf-media">
          <div class="vf-img">${phHTML('กำลังโหลดรูป',true)}</div>
          ${qty<=0?'<span class="vf-flag">สินค้าหมด</span>':''}
          <div class="vf-cap">
            <div class="vf-name" title="${eh(it.name)}">${eh(it.name)}</div>
            <div class="vf-company-name">${eh(coName)}</div>
          </div>
        </div>
        <div class="vf-body">
          <div class="vf-head">
            <div class="vf-head-txt"><strong>${eh(it.iditem)}</strong><span class="h-hover">ชี้เพื่อดูรายละเอียด</span><span class="h-touch">แตะเพื่อดูรายละเอียด</span></div>
            ${coChip}
          </div>
          <div class="vf-stats">
            <div><strong class="${qty<=0?'zero':''}">${qty.toLocaleString('en-US')}</strong><small>คงเหลือ</small></div>
            <div><strong title="${eh(brand)}">${eh(brand||'-')}</strong><small>ยี่ห้อ</small></div>
            <div><strong>${eh(typeLabel)}</strong><small>ประเภท</small></div>
          </div>
        </div>
      </div>
      <div class="v-face v-back">
        <div class="vb-top"><span class="vb-id">${eh(it.iditem)}</span>${coChip}</div>
        <div class="vb-qty"><small>คงเหลือในคลัง</small><strong class="${qty<=0?'zero':''}">${qty.toLocaleString('en-US')}</strong></div>
        <dl class="vb-spec">
          <div><dt>ยี่ห้อ</dt><dd title="${eh(brand)}">${eh(brand||'-')}</dd></div>
          <div><dt>ประเภท</dt><dd>${eh(typeLabel)}</dd></div>
          <div><dt>บริษัท</dt><dd title="${eh(coName)}">${eh(coName)}</dd></div>
        </dl>
        <p class="vb-note">กำลังโหลดหมายเหตุ...</p>
        <button class="vb-cta" onclick="event.stopPropagation();openTx('${idJs}','${ej(it.name)}')">ดูประวัติเข้า-ออก ${ICON.chev(16)}</button>
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

// แสดงการ์ดทั้งหน้าทันที (รูปเป็นโครงรอไว้) แล้วค่อยโหลดรูปทีละใบตามลำดับจากบนลงล่าง
async function render(seq){
  const frag=document.createDocumentFragment();
  const jobs=[];
  products.forEach(item=>{
    const key=item._pid||item.iditem;
    if(!HIDE_IDITEMS.includes(item.iditem)){
      const c=buildCard(item,null,false);
      frag.appendChild(c);jobs.push([c,item.iditem,item.image]);
    }
    (subs[key]||[]).forEach(sub=>{
      if(HIDE_IDITEMS.includes(sub.iditem)) return;
      const c=buildCard(sub,item,true);
      frag.appendChild(c);jobs.push([c,sub.iditem,sub.image]);
    });
  });

  if(seq!==renderSeq) return false;
  if(!jobs.length){
    const q=[currentFilters.name,currentFilters.brand].filter(Boolean).join(' / ');
    $('tb').innerHTML=q
      ?`<div class="empty"><h3>ไม่พบสินค้าที่ตรงกับ “${eh(q)}”</h3><p>ลองพิมพ์ชื่อให้สั้นลง หรือเลือกทุกยี่ห้อ</p><button type="button" onclick="clearFilter()">ล้างการค้นหา</button></div>`
      :`<div class="empty"><h3>ยังไม่มีสินค้าในคลัง</h3><p>สินค้าที่เพิ่มในระบบจะแสดงที่นี่</p></div>`;
    renderPg();
    return true;
  }

  // การ์ดที่เคยโหลดแล้ว แสดงรูป/หมายเหตุเดิมทันที (ตอนรีเฟรชอัตโนมัติจะได้ไม่กระพริบ)
  jobs.forEach(([c,id])=>{
    const k=cardCache.get(id);
    if(!k) return;
    setCardNote(c,k.note,k.title);
    if(k.src) showCardImage(c,k.src,true);
  });
  if(!window.__revealed){
    window.__revealed=true;
    [...frag.children].slice(0,16).forEach((c,i)=>{c.classList.add('rise');c.style.setProperty('--i',i);c.addEventListener('animationend',()=>c.classList.remove('rise'),{once:true});});
  }
  $('tb').replaceChildren(frag);
  renderPg();
  loadCardsInOrder(jobs,seq); // ไม่ต้องรอ ให้รูปทยอยขึ้นเอง
  return true;
}

// โหลดรูปตามลำดับการ์ด ครั้งละ IMG_CONCURRENCY ใบ (ตั้งเป็น 1 = โหลดทีละใบจริงๆ)
// ถ้าผู้ใช้ค้นหาใหม่/เปลี่ยนหน้า คิวเก่าจะหยุดเอง
const IMG_CONCURRENCY=4;
async function loadCardsInOrder(jobs,seq){
  let i=0;
  const worker=async()=>{
    while(i<jobs.length&&seq===renderSeq){
      const [c,id,img]=jobs[i++];
      try{await loadCardTx(c,id,img);}catch(e){console.warn('load card',id,e);}
    }
  };
  await Promise.all(Array.from({length:Math.min(IMG_CONCURRENCY,jobs.length)},worker));
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

// เวอร์ชันรูป: ถ้าเปลี่ยนรูปใน Google Drive โดยใช้ไฟล์เดิม ลิงก์จะเหมือนเดิม เบราว์เซอร์/Google จึงยังส่งรูปเก่ามา
// กดปุ่ม "โหลดรูปใหม่" แล้วเลขนี้จะเปลี่ยน ลิงก์รูปทุกใบจะต่างจากเดิม ทำให้ต้องดึงรูปล่าสุด (ใช้ร่วมกับหน้ารายการเข้า-ออก)
let IMG_V='';try{IMG_V=localStorage.getItem('3e-imgv')||'';}catch(e){}
function withImgV(u){
  if(!u||!IMG_V||u.startsWith('data:')) return u;
  return u+(u.includes('?')?'&':'?')+'v='+IMG_V;
}
const cardCache=new Map(); // iditem → {note,title,src} จำผลที่โหลดแล้วไว้ใช้ตอนแสดงซ้ำ

// โครงรูป: กำลังโหลด = รถกระบะวิ่ง (ข้อความซ่อนไว้ให้โปรแกรมอ่านหน้าจอ) / ไม่มีรูป-โหลดไม่ได้ = รถจอดนิ่ง + ข้อความ
const PH_CAR='<svg class="ph-car" viewBox="0 0 72 36" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path class="beam" d="M66 18.5 L84 14 L84 24 Z"/><g class="body"><path d="M5 24 V17 H27 V11.5 Q27 10 28.5 10 H42 Q44 10 45.5 11.5 L50.5 16.5 H59 Q65 16.5 65 21.5 V24 H58.6 A6.6 6.6 0 0 0 45.4 24 H24.6 A6.6 6.6 0 0 0 11.4 24 Z"/><path d="M30 12.5 H41.6 L46 16.5 H30 Z"/><path d="M5 17 V14.5"/><path d="M36 16.5 V23"/><circle class="lamp" cx="63.2" cy="19.6" r="1.1"/></g><g class="wheel"><circle cx="18" cy="25" r="4.6"/><path d="M18 21.2 V28.8 M14.2 25 H21.8"/></g><g class="wheel"><circle cx="52" cy="25" r="4.6"/><path d="M52 21.2 V28.8 M48.2 25 H55.8"/></g><path class="road" d="M0 32.5 H72" stroke-width="1.2" opacity=".55"/></svg>';
function phHTML(txt,loading){
  return loading
    ?`<div class="vf-ph loading" role="img" aria-label="${eh(txt)}">${PH_CAR}<span class="sr">${eh(txt)}</span></div>`
    :`<div class="vf-ph">${PH_CAR}<span>${eh(txt)}</span></div>`;
}
function setCardNote(card,note,title){
  const el=card.querySelector('.vb-note');
  el.textContent=note;
  el.title=title||'';
}

// แสดงรูปบนการ์ด: รอรูปโหลดเสร็จก่อนค่อยสลับจากโครงรูป แล้วค่อยๆ เฟดขึ้นมา
// instant=true ใช้กับรูปที่เคยโหลดแล้ว (อยู่ใน cache ของเบราว์เซอร์) แสดงทันทีไม่ต้องเฟด
function showCardImage(card,src,instant){
  const media=card.querySelector('.vf-img');
  const cur=media.querySelector('img');
  if(cur&&cur.dataset.src===src) return Promise.resolve('load');
  const img=new Image();
  img.alt='';
  img.decoding='async';
  img.dataset.src=src;
  img.src=withImgV(toDirectImageUrl(src));
  if(instant){
    img.classList.add('ready');
    media.replaceChildren(img);
    return Promise.resolve('load');
  }
  return waitImg(img).then(st=>{
    if(!card.isConnected) return st;
    if(st==='error'){
      if(!cur) media.innerHTML=phHTML('โหลดรูปไม่ได้');
      return st;
    }
    media.replaceChildren(img);
    if(st==='load'){
      void img.offsetWidth; // ให้เบราว์เซอร์วาดตอนโปร่งใสก่อน ภาพจะได้เฟดขึ้น
      img.classList.add('ready');
    }else{
      // รูปช้าผิดปกติ: ปล่อยให้โหลดต่อเบื้องหลัง เสร็จเมื่อไรก็เฟดขึ้นเอง
      img.addEventListener('load',()=>img.classList.add('ready'),{once:true});
      img.addEventListener('error',()=>{if(img.isConnected) media.innerHTML=phHTML('โหลดรูปไม่ได้');},{once:true});
    }
    return st;
  });
}

// โหลดหมายเหตุจาก transaction + แสดงรูป (รูปของสินค้าก่อน ถ้าไม่มีค่อยใช้รูปจาก Transaction)
async function loadCardTx(card,id,itemImage){
  const media=card.querySelector('.vf-img');
  let rows=null;
  try{
    const r=await API.get('/api/transaction/by-item/'+encodeURIComponent(id));
    rows=Array.isArray(r)?r:[];
  }catch(e){}
  if(!card.isConnected) return; // หน้าถูกแสดงใหม่ไปแล้ว

  const prev=cardCache.get(id);
  if(rows===null){ // โหลด transaction ไม่สำเร็จ ใช้ข้อมูลเดิมถ้ามี
    if(!prev) setCardNote(card,'-');
    const src=itemImage||prev?.src;
    if(src) await showCardImage(card,src);
    else media.innerHTML=phHTML('โหลดรูปไม่สำเร็จ');
    return;
  }

  const notes=[...new Set(rows.map(r=>r['หมายเหตุ']).filter(Boolean))];
  const note=notes.length?notes.join(' / '):'ไม่มีหมายเหตุ';
  const title=notes.join(' / ');
  setCardNote(card,note,title);

  const src=itemImage||rows.map(r=>r['รูปประกอบ']).find(Boolean)||'';
  let shown='';
  if(src){
    const st=await showCardImage(card,src);
    if(st!=='error') shown=src;
  }else{
    media.innerHTML=phHTML('ยังไม่มีรูปสินค้า');
  }
  cardCache.set(id,{note,title,src:shown});
}

// ปุ่มโหลดรูปใหม่: เปลี่ยนเวอร์ชันรูป ล้างที่จำไว้ แล้ววาดหน้าปัจจุบันใหม่ รูปทุกใบจะถูกดึงจาก Google Drive ใหม่
async function reloadImages(){
  const b=$('imgBtn');
  if(b.disabled) return;
  IMG_V=Date.now().toString(36);
  try{localStorage.setItem('3e-imgv',IMG_V);}catch(e){}
  cardCache.clear();
  b.disabled=true;b.classList.add('spin');
  try{
    await renderPage(pg,++renderSeq); // อยู่ตำแหน่งเดิม ไม่เลื่อนขึ้นบน
    toast('กำลังโหลดรูปล่าสุดจาก Google Drive');
  }finally{
    setTimeout(()=>{b.disabled=false;b.classList.remove('spin');},800);
  }
}

function renderPg(){
  const tot=Math.max(1,Math.ceil(totalItems/PG));
  const el=$('paging');
  el.innerHTML='';
  if(tot<=1) return;
  const btn=(html,label,fn)=>{const b=document.createElement('button');b.className='pg-btn';b.innerHTML=html;b.setAttribute('aria-label',label);b.onclick=fn;el.appendChild(b);};
  if(pg>1) btn(`${ICON.chevL(16)}<span>ก่อนหน้า</span>`,'หน้าก่อนหน้า',()=>goPage(pg-1));
  const s=document.createElement('span');
  s.className='pg-info';
  s.innerHTML=`หน้า <b>${pg}</b> จาก ${tot} (${totalItems.toLocaleString('en-US')} รายการ)`;
  el.appendChild(s);
  if(pg<tot) btn(`<span>ถัดไป</span>${ICON.chev(16)}`,'หน้าถัดไป',()=>goPage(pg+1));
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
    $('txBody').innerHTML=`<div class="tx-empty">โหลดประวัติไม่สำเร็จ ลองปิดแล้วเปิดใหม่อีกครั้ง</div>`;
    $('txFoot').textContent='';
  }
}
function renderTxModal(data){
  const body=$('txBody'),foot=$('txFoot');
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
    h+=`<tr><td style="white-space:nowrap">${r.Timestamp||'-'}</td><td>${r['ชื่อผู้ดำเนินงาน']||'-'}</td><td><span class="tx-type ${tc}">${r['ประเภทข้อมูล']||'-'}</span></td><td>${r['หมายเลขเอกสาร']||'-'}</td><td>${r['รายการ']||'-'}</td><td class="num">${r['จำนวน']!==''?r['จำนวน']:'-'}</td><td class="num">${ROLE==='viewer'?'-':(r['ราคาต่อหน่วย']!==''?r['ราคาต่อหน่วย']:'-')}</td><td>${r['ชั้นวาง']||'-'}</td><td>${r['หมายเหตุ']||'-'}</td><td>${pic}</td></tr>`;
  });
  h+='</tbody></table>';
  body.innerHTML=h;
}
function closeTx(){
  $('txOv').classList.remove('on');
  flushItemsPendingRefresh();
}

/* ========== คีย์ลัด: Esc ปิดหน้าต่าง, / ไปที่ช่องค้นหา ========== */
document.addEventListener('keydown',e=>{
  if(e.key==='Escape'){
    if($('txOv').classList.contains('on')) closeTx();
    return;
  }
  if(e.key==='/'&&!e.ctrlKey&&!e.metaKey&&!e.altKey){
    const tag=document.activeElement?.tagName||'';
    if(/^(INPUT|TEXTAREA|SELECT)$/.test(tag)||$('txOv').classList.contains('on')) return;
    e.preventDefault();
    $('sName').focus();
  }
});


/* ========== สลับธีมมืด/สว่าง (จำค่าไว้ในเครื่อง) ========== */
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

/* ========== ขอบการ์ดเรืองแสงตามเมาส์ ========== */
(function initGlow(){
  if(!window.matchMedia('(hover: hover)').matches) return;
  const grid=$('tb');let raf=0,px=0,py=0;
  const paint=()=>{
    raf=0;
    grid.querySelectorAll('.v-card').forEach(c=>{
      const r=c.getBoundingClientRect();
      if(r.bottom<-300||r.top>innerHeight+300) return;
      const x=px-r.left;
      c.style.setProperty('--mx',x+'px');
      c.style.setProperty('--mxr',(r.width-x)+'px');
      c.style.setProperty('--my',(py-r.top)+'px');
    });
  };
  grid.addEventListener('pointermove',e=>{px=e.clientX;py=e.clientY;grid.classList.add('glow');if(!raf) raf=requestAnimationFrame(paint);});
  grid.addEventListener('pointerleave',()=>grid.classList.remove('glow'));
})();

/* ========== Auto-refresh (SSE) ========== */
let itemsRefreshPending=false;
let itemsRefreshDebounce=null;

function isBusyViewing(){
  return $('txOv').classList.contains('on');
}

function requestItemsSilentRefresh(){
  clearTimeout(itemsRefreshDebounce);
  itemsRefreshDebounce=setTimeout(()=>{
    if(isBusyViewing()){itemsRefreshPending=true;return;}
    fetchAllAndRender(false,true);
  },300);
}

function flushItemsPendingRefresh(){
  if(!itemsRefreshPending||isBusyViewing()) return;
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