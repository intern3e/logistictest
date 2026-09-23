<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คู่มือการใช้งาน - Dashboard Manual</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Prompt', sans-serif; }
        /* กันหน้าเลื่อนซ้ายขวาเวลาเปลี่ยนเมนู (scrollbar โผล่/หาย) ให้ทุกเมนูตำแหน่งเท่ากัน */
        html { overflow-y: scroll; scrollbar-gutter: stable; }
        /* ปุ่มในสารบัญ: ขนาดเท่ากันทุกอัน อยู่บรรทัดเดียว */
        aside ul button { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; min-height: 40px; }
        [x-cloak] { display: none !important; }

        /* รูปในคู่มือกดเพื่อขยายได้ */
        main img { cursor: zoom-in; transition: opacity .15s; }
        /* กรอบรูปทุกอันขนาดเท่ากัน */
        main li img { width: 100%; height: 12rem; object-fit: contain; display: block; }
        main img:hover { opacity: .85; }

        /* ===== Lightbox ขยายรูป ===== */
        #lightbox {
            position: fixed; inset: 0; z-index: 9999;
            background: rgba(15, 23, 42, .88);
            display: none; align-items: center; justify-content: center;
            padding: 24px;
        }
        #lightbox.open { display: flex; }
        #lightbox img {
            max-width: 95vw; max-height: 88vh;
            object-fit: contain; border-radius: 8px;
            background: #fff; box-shadow: 0 10px 40px rgba(0,0,0,.4);
            cursor: zoom-out;
        }
        #lightbox .lb-btn {
            position: absolute; background: rgba(255,255,255,.92); color: #1f2937;
            border: none; border-radius: 9999px; width: 44px; height: 44px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,.25);
        }
        #lightbox .lb-btn:hover { background: #fff; }
        #lightbox .lb-close { top: 16px; right: 16px; }
        #lightbox .lb-prev { left: 16px; top: 50%; transform: translateY(-50%); }
        #lightbox .lb-next { right: 16px; top: 50%; transform: translateY(-50%); }
        #lightbox .lb-caption {
            position: absolute; bottom: 14px; left: 50%; transform: translateX(-50%);
            color: #e5e7eb; font-size: 13px; text-align: center; white-space: nowrap;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased" x-data="{ activeTab: null }"
      x-init="$watch('activeTab', () => $nextTick(() => document.getElementById('manual-grid').scrollIntoView({ behavior: 'smooth', block: 'start' })))">

    <main class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8 space-y-8">
        
        <div class="text-center bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <h1 class="text-2xl font-bold text-gray-800 flex items-center justify-center">
                <span class="mr-2 text-2xl">📖</span> คู่มือการใช้งานระบบ (Dashboard Manual)
            </h1>
            <p class="text-gray-500 text-sm mt-1">รายละเอียดและขั้นตอนการใช้งานฟังก์ชันต่างๆ ภายในระบบบริหารจัดการ</p>
        </div>

        <div id="manual-grid" class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start" style="scroll-margin-top: 24px;">
            
            <aside class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6 border border-gray-200">
                    <h2 class="font-semibold text-gray-900 mb-4 text-base border-b pb-2 flex items-center justify-between text-blue-600">
                        <span> สารบัญคู่มือ</span>
                        <button @click="activeTab = null" class="text-xs text-gray-400 hover:text-blue-600 underline font-normal">แสดงทั้งหมด</button>
                    </h2>
                    <ul class="space-y-2.5 text-sm">
                        <li>
                            <button @click="activeTab = 'billing'" :class="activeTab === 'billing' ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-600 hover:text-blue-600'" class="w-full text-left px-3 py-2 rounded-md transition">
                                1. วิธีสร้างบิลส่งของ (Billing)
                            </button>
                        </li>
                        <li>
                            <button @click="activeTab = 'deposit'" :class="activeTab === 'deposit' ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-600 hover:text-blue-600'" class="w-full text-left px-3 py-2 rounded-md transition">
                                2. วิธีสร้างบิลมัดจำ (Deposit)
                            </button>
                        </li>
                        <li>
                            <button @click="activeTab = 'rec'" :class="activeTab === 'rec' ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-600 hover:text-blue-600'" class="w-full text-left px-3 py-2 rounded-md transition">
                                3. วิธีสร้างใบชั่วคราว (Rec)
                            </button>
                        </li>
                        <li>
                            <button @click="activeTab = 'inventory'" :class="activeTab === 'inventory' ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-600 hover:text-blue-600'" class="w-full text-left px-3 py-2 rounded-md transition">
                                4. ค้นหาสินค้า (Inventory)
                            </button>
                        </li>
                        <li>
                            <button @click="activeTab = 'sale-shelf'" :class="activeTab === 'sale-shelf' ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-600 hover:text-blue-600'" class="w-full text-left px-3 py-2 rounded-md transition">
                                5. ชั้นวาง Sale
                            </button>
                        </li>
                        <li>
                            <button @click="activeTab = 'support'" :class="activeTab === 'support' ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-600 hover:text-blue-600'" class="w-full text-left px-3 py-2 rounded-md transition">
                                6. ติดต่อผู้ดูแลระบบ
                            </button>
                        </li>
                    </ul>
                </div>
            </aside>

            <div class="lg:col-span-3 flex flex-col gap-6">
                
                <section x-show="activeTab === null || activeTab === 'billing'" x-transition class="bg-white rounded-xl shadow-sm p-6 sm:p-8 border border-gray-200 space-y-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                            <span class="bg-blue-100 text-blue-600 px-2.5 py-1 rounded-md mr-3 text-xs font-semibold">01</span> 
                            วิธีสร้างบิลส่งของ (Billing)
                        </h2>
                        <p class="text-gray-600 text-sm mb-4">ขั้นตอนการออกบิลส่งของหรือใบแจ้งหนี้ในระบบ มีขั้นตอนดังนี้:</p>
                        <ol class="list-decimal list-inside space-y-4 text-sm text-gray-600">
                            <li>
                                เลือกเมนู <span class="bg-blue-600 text-white px-2 py-0.5 rounded text-xs font-semibold shadow-sm inline-block my-1">Sale Order</span> ในโปรแกรม myAccount จากนั้นให้ไปที่เมนู <span class="bg-blue-600 text-white px-2 py-0.5 rounded text-xs font-semibold shadow-sm inline-block my-1">ขายเชื่อ</span>
                                
                                <div class="mt-3 mb-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm">
                                        <img src="https://lh3.googleusercontent.com/d/1YRNHiaZYtsTrEtZXpiL51ziukmZ2KiL4" alt="ตัวอย่างภาพที่ 1" class="w-full h-48 rounded object-contain bg-gray-100">
                                    </div>
                                    <div class="border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm">
                                        <img src="https://lh3.googleusercontent.com/d/15Bsk6_jbL8XDfJ2rrxUHhh0DVo16UCwX" alt="ตัวอย่างภาพที่ 2" class="w-full h-48 rounded object-contain bg-gray-100">
                                    </div>
                                </div>
                                                                            
                                <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mt-4">
                                    <h3 class="text-sm font-bold text-blue-900 mb-3 flex items-center">
                                        <span class="mr-2">⚠️</span> 
                                        ข้อควรระวังและเงื่อนไขสำคัญในการออกบิล
                                    </h3>
                                    <ul class="space-y-2 text-sm text-blue-900">
                                        <li class="flex items-start">
                                            <span class="text-blue-600 font-bold mr-2">•</span>
                                            <span>เปิดบิลส่งของล่วงหน้า <strong>1 วันเท่านั้น</strong> (เช่น ต้องการส่งของวันที่ 2 ให้สร้างบิลส่งของวันที่ 1 หรือตามที่ในกลุ่มประกาศ)</span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="text-blue-600 font-bold mr-2">•</span>
                                            <span>ทุกเวลา <strong>10:30 น.</strong> ระบบจะทำการเปลี่ยนวันที่ของใบส่งของโดยอัตโนมัติ โปรดตรวจสอบวันที่ให้ถูกต้องก่อนบันทึก</span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="text-blue-600 font-bold mr-2">•</span>
                                            <span>ในกรณีที่มีการขายทั้ง <strong>สินค้า, บริการ, และค่าเช่า</strong> จะต้องดำเนินการ <strong>สร้างบิลส่งของแยกออกจากกัน</strong> ห้ามรวมอยู่ในบิลเดียวกัน</span>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li>
                               ไปที่ <span class="bg-blue-600 text-white px-2 py-0.5 rounded text-xs font-semibold shadow-sm inline-block my-1">Create SO</span> สร้าง SO ใน SERVER เเละ ค้นหา <span class="bg-blue-600 text-white px-2 py-0.5 rounded text-xs font-semibold shadow-sm inline-block my-1">ค้นหา SO</span>
                                
                                <div class="mt-3 mb-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm">
                                        <img src="https://lh3.googleusercontent.com/d/1uij-kIlpZqU5DLc7W0NxUz7d2m2lnsVB" alt="ตัวอย่างการค้นหา SO" class="w-full h-48 rounded object-contain bg-gray-100">
                                    </div>
                                    <div class="border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm">
                                        <img src="https://lh3.googleusercontent.com/d/1QYqpGOCYTKyRY7g8MJyCZA1lONS3omUS" alt="ตัวอย่างการเพิ่ม SO ใน Server" class="w-full h-48 rounded object-contain bg-gray-100">
                                    </div>
                                </div>
                            </li>
                            <li>
                                เลือก <span class="bg-blue-600 text-white px-2 py-0.5 rounded text-xs font-semibold shadow-sm inline-block my-1">PO</span> ใน SO ที่ต้องการสร้างบิล กดไปที่ <span class="bg-blue-600 text-white px-2 py-0.5 rounded text-xs font-semibold shadow-sm inline-block my-1">บันทึกข้อมูลจัดส่ง</span> มีขั้นตอนดังนี้:
                                
                                <!-- รายการย่อย 3.1 - 3.6 พร้อมกรอบ (เอา bg-white ออก) -->
                                <div class="mt-4 mb-4 border border-gray-200 rounded-lg bg-gray-50 p-4">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                        <div class="border border-gray-200 rounded-md px-3 py-2.5 text-xs text-gray-700 hover:border-blue-300 transition">
                                            <span class="font-semibold text-blue-600 mr-1">3.1</span> กรอกชื่อผู้ติดต่อ
                                        </div>
                                        <div class="border border-gray-200 rounded-md px-3 py-2.5 text-xs text-gray-700 hover:border-blue-300 transition">
                                            <span class="font-semibold text-blue-600 mr-1">3.2</span> กรอกละติจูด ลองจิจูด เพื่อแสดงแผนที่
                                        </div>
                                        <div class="border border-gray-200 rounded-md px-3 py-2.5 text-xs text-gray-700 hover:border-blue-300 transition">
                                            <span class="font-semibold text-blue-600 mr-1">3.3</span> รายละเอียดเพิ่มเติม (สำหรับคนขับ)
                                        </div>
                                        <div class="border border-gray-200 rounded-md px-3 py-2.5 text-xs text-gray-700 hover:border-blue-300 transition">
                                            <span class="font-semibold text-blue-600 mr-1">3.4</span> ประเภทสินค้า / บริการ
                                        </div>
                                        <div class="border border-gray-200 rounded-md px-3 py-2.5 text-xs text-gray-700 hover:border-blue-300 transition">
                                            <span class="font-semibold text-blue-600 mr-1">3.5</span> อัปโหลดเอกสาร PO
                                        </div>
                                        <div class="border border-gray-200 rounded-md px-3 py-2.5 text-xs text-gray-700 hover:border-blue-300 transition">
                                            <span class="font-semibold text-blue-600 mr-1">3.6</span> บันทึกเส้นทางส่งสินค้า
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3 mb-2 grid grid-cols-1 sm:grid-cols-2 gap-4">

                                    <div class="border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm">
                                        <img src="https://lh3.googleusercontent.com/d/1jr18bqS8O5tBH3eRu96hz8ir_yAYFzuY" alt="ขั้นตอนบันทึกข้อมูลจัดส่ง 1" class="w-full h-48 rounded object-contain bg-gray-100">
                                    </div>

                                    <div class="relative group min-w-0">
                                        <button onclick="document.getElementById('slider-steps').scrollBy({ left: -document.getElementById('slider-steps').offsetWidth, behavior: 'smooth' })" class="absolute left-2 top-1/2 -translate-y-1/2 z-10 bg-white/90 hover:bg-white text-gray-800 p-2 rounded-full shadow-md transition-all h-10 w-10 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                            </svg>
                                        </button>

                                        <div id="slider-steps" class="flex overflow-x-auto snap-x snap-mandatory scroll-smooth [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
                                            <div class="flex-shrink-0 w-full border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm snap-center">
                                                <img src="https://lh3.googleusercontent.com/d/1ElcaGLT84YaXa3K_6DJnAdTtKDYImtMo" alt="ขั้นตอนบันทึกข้อมูลจัดส่ง 3" class="w-full h-48 rounded object-contain bg-gray-100">
                                            </div>
                                            <div class="flex-shrink-0 w-full border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm snap-center">
                                                <img src="https://lh3.googleusercontent.com/d/130OZqezkrtjH7eUoCkLAjocBkg4TLqri" alt="ขั้นตอนบันทึกข้อมูลจัดส่ง 2" class="w-full h-48 rounded object-contain bg-gray-100">
                                            </div>
                                            <div class="flex-shrink-0 w-full border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm snap-center">
                                                <img src="https://lh3.googleusercontent.com/d/1pADyWBlGO2em5-Q-EXRkH5Tu43xs_KK4" alt="ขั้นตอนบันทึกข้อมูลจัดส่ง 4" class="w-full h-48 rounded object-contain bg-gray-100">
                                            </div>
                                            <div class="flex-shrink-0 w-full border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm snap-center">
                                                <img src="https://lh3.googleusercontent.com/d/1ruN-AfEY7J4nmru90C9as_JrSX1UAnFm" alt="ขั้นตอนบันทึกข้อมูลจัดส่ง 5" class="w-full h-48 rounded object-contain bg-gray-100">
                                            </div>
                                        </div>

                                        <button onclick="document.getElementById('slider-steps').scrollBy({ left: document.getElementById('slider-steps').offsetWidth, behavior: 'smooth' })" class="absolute right-2 top-1/2 -translate-y-1/2 z-10 bg-white/90 hover:bg-white text-gray-800 p-2 rounded-full shadow-md transition-all h-10 w-10 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </li>
                            <li>
                                บันทึกเส้นทางส่งสินค้าเสร็จสิ้น ข้อมูลจะขึ้นใน <span class="bg-blue-600 text-white px-2 py-0.5 rounded text-xs font-semibold shadow-sm inline-block my-1">Billing</span>
                                <div class="mt-3 mb-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm">
                                        <img src="https://lh3.googleusercontent.com/d/14Bo68P9SWNE-r_VpCAFPjrfcN_n83tKo" alt="ตัวอย่างภาพที่ 1" class="w-full h-48 rounded object-contain bg-gray-100">
                                    </div>
                                    <div class="border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm">
                                        <img src="https://lh3.googleusercontent.com/d/1Hq8S4LpWewdisagsE-CESRGmWikLCjYL" alt="ตัวอย่างภาพที่ 2" class="w-full h-48 rounded object-contain bg-gray-100">
                                    </div>
                                </div>
                            </li>
                        </ol>
                    </div>
                </section>

                <section x-show="activeTab === null || activeTab === 'deposit'" x-transition class="bg-white rounded-xl shadow-sm p-6 sm:p-8 border border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                        <span class="bg-blue-100 text-blue-600 px-2.5 py-1 rounded-md mr-3 text-xs font-semibold">02</span> 
                        วิธีสร้างบิลมัดจำ (Deposit)
                    </h2>
                    <p class="text-gray-600 text-sm mb-4">ขั้นตอนการออกบิลมัดจำในระบบ มีขั้นตอนดังนี้:</p>
                    
                    <ol class="list-decimal list-inside text-gray-600 text-sm space-y-6">
                        <li class="pl-2">
                            <span>ไปที่ <span class="bg-blue-600 text-white px-2 py-0.5 rounded text-xs font-semibold shadow-sm inline-block my-1">Create SO</span> สร้าง SO ใน Server และทำการ <span class="bg-blue-600 text-white px-2 py-0.5 rounded text-xs font-semibold shadow-sm inline-block my-1">ค้นหา SO</span></span>
                            
                            <div class="mt-3 mb-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm">
                                    <img src="https://lh3.googleusercontent.com/d/1uij-kIlpZqU5DLc7W0NxUz7d2m2lnsVB" alt="ตัวอย่างการค้นหา SO" class="w-full h-48 rounded object-contain bg-gray-100">
                                </div>
                                <div class="border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm">
                                    <img src="https://lh3.googleusercontent.com/d/1QYqpGOCYTKyRY7g8MJyCZA1lONS3omUS" alt="ตัวอย่างการเพิ่ม SO ใน Server" class="w-full h-48 rounded object-contain bg-gray-100">
                                </div>
                            </div>
                        </li>

                        <li class="pl-2">
                            <span>ไปที่สร้างใบมัดจำ เพื่อทำการเปิดบิลใบมัดจำ <span class="bg-blue-600 text-white px-2 py-0.5 rounded text-xs font-semibold shadow-sm inline-block my-1">Deposit</span></span>
                            
                            <div class="mt-4 mb-4 border border-gray-200 rounded-lg bg-gray-50 p-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                    <div class="border border-gray-200 rounded-md px-3 py-2.5 text-xs text-gray-700 hover:border-blue-300 transition">
                                        <span class="font-semibold text-blue-600 mr-1">2.1</span> ชื่อผู้ติดต่อ
                                    </div>
                                    <div class="border border-gray-200 rounded-md px-3 py-2.5 text-xs text-gray-700 hover:border-blue-300 transition">
                                        <span class="font-semibold text-blue-600 mr-1">2.2</span> กำหนดอัตรามัดจำสินค้า หรือ บริการ
                                    </div>
                                    <div class="border border-gray-200 rounded-md px-3 py-2.5 text-xs text-gray-700 hover:border-blue-300 transition">
                                        <span class="font-semibold text-blue-600 mr-1">2.3</span> เลือกรายการสินค้า
                                    </div>
                                    <div class="border border-gray-200 rounded-md px-3 py-2.5 text-xs text-gray-700 hover:border-blue-300 transition">
                                        <span class="font-semibold text-blue-600 mr-1">2.4</span> บันทึกใบมัดจำ
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 mb-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm">
                                    <img src="https://lh3.googleusercontent.com/d/1jr18bqS8O5tBH3eRu96hz8ir_yAYFzuY" alt="ตัวอย่าง Deposit" class="w-full h-48 rounded object-contain bg-gray-100">
                                </div>

                                <div class="relative group min-w-0 border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm overflow-hidden">
                                    <button type="button" onclick="document.getElementById('slider-deposit').scrollBy({ left: -document.getElementById('slider-deposit').offsetWidth, behavior: 'smooth' })" class="absolute left-2 top-1/2 -translate-y-1/2 z-10 bg-white/90 hover:bg-white text-gray-800 p-2 rounded-full shadow-md transition-all h-10 w-10 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <div id="slider-deposit" class="flex overflow-x-auto snap-x snap-mandatory scroll-smooth [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
                                        <div class="flex-shrink-0 w-full snap-center">
                                            <img src="https://lh3.googleusercontent.com/d/1RqO--WsROoyuCROALcvB9BWQj8WU1bA3" alt="ขั้นตอน Deposit 1" class="w-full h-48 rounded object-contain bg-gray-100">
                                        </div>
                                        <div class="flex-shrink-0 w-full snap-center">
                                            <img src="https://lh3.googleusercontent.com/d/1ijY4clAMXe2MBIhxDh3mgo0wnar7cQP_" alt="ขั้นตอน Deposit 2" class="w-full h-48 rounded object-contain bg-gray-100">
                                        </div>
                                    </div>
                                    <button type="button" onclick="document.getElementById('slider-deposit').scrollBy({ left: document.getElementById('slider-deposit').offsetWidth, behavior: 'smooth' })" class="absolute right-2 top-1/2 -translate-y-1/2 z-10 bg-white/90 hover:bg-white text-gray-800 p-2 rounded-full shadow-md transition-all h-10 w-10 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </li>
                                            <ol class="list-decimal list-inside text-gray-600 text-sm space-y-6">
                        <li class="pl-2">
                            <span>ไปที่ <span class="bg-blue-600 text-white px-2 py-0.5 rounded text-xs font-semibold shadow-sm inline-block my-1">Deposit Invoice</span> เพื่อดูรายการบิลใบมัดจำที่เราสร้าง เเละ รับ <span class="bg-blue-600 text-white px-2 py-0.5 rounded text-xs font-semibold shadow-sm inline-block my-1">PDF</span></span> ใบมัดจำ รวม ถึงเเนบหลักฐานการโอนเงิน
                            
                            <div class="mt-3 mb-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm">
                                    <img src="https://lh3.googleusercontent.com/d/1FxV14O7x0BcRdzYBnAE6OcdEvXaVIDgU" alt="ตัวอย่างการค้นหา SO" class="w-full h-48 rounded object-contain bg-gray-100">
                                </div>
                            <div class="border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm">
                                <img src="https://lh3.googleusercontent.com/d/1zysNKY5LAkVbEDoWNkb-fWL89j7JWrxn" alt="ตัวอย่างการเพิ่ม SO ใน Server" class="w-full h-48 rounded object-contain bg-gray-100">
                            </div>
                            </div>
                        </li>
                    </ol>
                </section>

                <section x-show="activeTab === null || activeTab === 'rec'" x-transition class="bg-white rounded-xl shadow-sm p-6 sm:p-8 border border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                        <span class="bg-blue-100 text-blue-600 px-2.5 py-1 rounded-md mr-3 text-xs font-semibold">03</span>
                        วิธีสร้างใบชั่วคราว (Rec)
                    </h2>
                    <p class="text-gray-600 text-sm mb-4">ขั้นตอนการออกบิลชั่วคราวในระบบ มีขั้นตอนดังนี้:</p>

                    <ol class="list-decimal list-inside text-gray-600 text-sm space-y-6">
                        <li class="pl-2">
                            <span>ไปที่เมนู <span class="bg-blue-600 text-white px-2 py-0.5 rounded text-xs font-semibold shadow-sm inline-block my-1">Bill ชั่วคราว</span> เพื่อเข้าไปหน้าบิลชั่วคราว จากนั้นให้กดที่ <span class="bg-blue-600 text-white px-2 py-0.5 rounded text-xs font-semibold shadow-sm inline-block my-1">สร้างเอกสารชั่วคราว</span></span> เพื่อเริ่มสร้างเอกสาร
                                                         <div class="mt-4 mb-4 border border-gray-200 rounded-lg bg-gray-50 p-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                    <div class="border border-gray-200 rounded-md px-3 py-2.5 text-xs text-gray-700 hover:border-blue-300 transition">
                                        <span class="font-semibold text-blue-600 mr-1">3.1</span>กำหนด วันที่ เเละ ประเภทบิล
                                    </div>
                                    <div class="border border-gray-200 rounded-md px-3 py-2.5 text-xs text-gray-700 hover:border-blue-300 transition">
                                        <span class="font-semibold text-blue-600 mr-1">3.2</span> เลือกชื่อบริษัทหัวเอกสาร
                                    </div>
                                    <div class="border border-gray-200 rounded-md px-3 py-2.5 text-xs text-gray-700 hover:border-blue-300 transition">
                                        <span class="font-semibold text-blue-600 mr-1">3.3</span> เลือกข้อมูลบริษัท ผู้ติดต่อ เบอร์ และพิกัด (ค้นหาอัตโนมัติด้วยเลข SO)
                                    </div>
                                    <div class="border border-gray-200 rounded-md px-3 py-2.5 text-xs text-gray-700 hover:border-blue-300 transition">
                                        <span class="font-semibold text-blue-600 mr-1">3.4</span> กรอกรายละเอียดเพิ่มเติม
                                    </div>
                                     <div class="border border-gray-200 rounded-md px-3 py-2.5 text-xs text-gray-700 hover:border-blue-300 transition">
                                        <span class="font-semibold text-blue-600 mr-1">3.5</span> เพิ่มสินค้าตามจำนวนที่ต้องการ
                                    </div>
                                    <div class="border border-gray-200 rounded-md px-3 py-2.5 text-xs text-gray-700 hover:border-blue-300 transition">
                                        <span class="font-semibold text-blue-600 mr-1">3.6</span> สร้างเอกสาร
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 mb-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm">
                                    <img src="https://lh3.googleusercontent.com/d/1zTUyVmsXL1E48ysoLKaSqa8wbT__CBvb" alt="ตัวอย่างการสร้างใบชั่วคราว (Rec) 1" class="w-full h-48 rounded object-contain bg-gray-100">
                                </div>
                                <div class="border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm">
                                    <img src="https://lh3.googleusercontent.com/d/1Gkh15hBmxGiCr00-VKXz5QJPHvOKco_N" alt="ตัวอย่างการสร้างใบชั่วคราว (Rec) 2" class="w-full h-48 rounded object-contain bg-gray-100">
                                </div>
                            </div>
                        </li>
                        <li class="pl-2">
                            <span>ไปที่เมนู <span class="bg-blue-600 text-white px-2 py-0.5 rounded text-xs font-semibold shadow-sm inline-block my-1">Bill ชั่วคราว</span> เพื่อเข้าไปหน้าบิลชั่วคราว จากนั้นให้กดที่ <span class="bg-blue-600 text-white px-2 py-0.5 rounded text-xs font-semibold shadow-sm inline-block my-1">สร้างเอกสารชั่วคราว</span></span> เพื่อเริ่มสร้างเอกสาร

                            <div class="mt-3 mb-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm">
                                    <img src="https://lh3.googleusercontent.com/d/1fM37ofvDkX0YRkzISOw_jsZnMyMsM-Lh" alt="ตัวอย่างขั้นตอนที่ 2 ภาพที่ 1" class="w-full h-48 rounded object-contain bg-gray-100">
                                </div>
                                <div class="border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm">
                                    <img src="https://lh3.googleusercontent.com/d/1BGmCyk5H6KUoDCcKw55qmOlQri59wrp8" alt="ตัวอย่างขั้นตอนที่ 2 ภาพที่ 2" class="w-full h-48 rounded object-contain bg-gray-100">
                                </div>
                            </div>
                        </li>
                    </ol>
                   
                </section>

                <section x-show="activeTab === null || activeTab === 'inventory'" x-transition class="bg-white rounded-xl shadow-sm p-6 sm:p-8 border border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                        <span class="bg-blue-100 text-blue-600 px-2.5 py-1 rounded-md mr-3 text-xs font-semibold">04</span>
                        วิธีค้นหาสินค้าในระบบ Inventory
                    </h2>
                    <p class="text-gray-600 text-sm mb-4">ขั้นตอนการค้นหาสินค้าในระบบ Inventory มีขั้นตอนดังนี้:</p>

                    <ol class="list-decimal list-inside text-gray-600 text-sm space-y-6">
                        <li class="pl-2">
                            <span>ไปที่เมนู <span class="bg-blue-600 text-white px-2 py-0.5 rounded text-xs font-semibold shadow-sm inline-block my-1">Inventory</span> จากนั้นเลือก <span class="bg-blue-600 text-white px-2 py-0.5 rounded text-xs font-semibold shadow-sm inline-block my-1">ค้นหาสินค้า</span> แล้วพิมพ์รหัสสินค้าหรือชื่อสินค้าเพื่อค้นหา</span>

                            <div class="mt-3 mb-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm">
                                    <img src="https://lh3.googleusercontent.com/d/1lbDzuEXo4ojhT8pfcSMit0tWGDUIfA-T" alt="ตัวอย่างการค้นหาสินค้า Inventory 1" class="w-full h-48 rounded object-contain bg-gray-100">
                                </div>
                                <div class="border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm">
                                    <img src="https://lh3.googleusercontent.com/d/13sb8ltBMyfBKDv8gSJikSZXhbNgZDKMW" alt="ตัวอย่างการค้นหาสินค้า Inventory 2" class="w-full h-48 rounded object-contain bg-gray-100">
                                </div>
                            </div>
                        </li>
                    </ol>
                </section>

                <section x-show="activeTab === null || activeTab === 'sale-shelf'" x-transition class="bg-white rounded-xl shadow-sm p-6 sm:p-8 border border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                        <span class="bg-blue-100 text-blue-600 px-2.5 py-1 rounded-md mr-3 text-xs font-semibold">05</span>
                        ชั้นวาง Sale
                    </h2>
                    <p class="text-gray-600 text-sm mb-4">ขั้นตอนการใช้งานชั้นวาง Sale มีขั้นตอนดังนี้:</p>

                    <ol class="list-decimal list-inside text-gray-600 text-sm space-y-6">
                        <li class="pl-2">
                            <span>ไปที่เมนู <span class="bg-blue-600 text-white px-2 py-0.5 rounded text-xs font-semibold shadow-sm inline-block my-1">MENU</span> จากนั้นเลือก <span class="bg-blue-600 text-white px-2 py-0.5 rounded text-xs font-semibold shadow-sm inline-block my-1">ชั้นวาง Sale</span> เพื่อเข้าสู่หน้าชั้นวาง Sale</span>

                            <div class="mt-3 mb-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm">
                                    <img src="https://lh3.googleusercontent.com/d/1KXnUgKd3diMCyLmZ47EUoU6U4QfQWedi" alt="ตัวอย่างชั้นวาง Sale 1" class="w-full h-48 rounded object-contain bg-gray-100">
                                </div>
                                <div class="border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm">
                                    <img src="https://lh3.googleusercontent.com/d/1RqcuYwxMyfOaQp4OYMg9CF-QBC4qo-YF" alt="ตัวอย่างชั้นวาง Sale 2" class="w-full h-48 rounded object-contain bg-gray-100">
                                </div>
                            </div>
                        </li>
                    </ol>
                </section>

                <section x-show="activeTab === null || activeTab === 'support'" x-transition class="bg-white rounded-xl shadow-sm p-6 sm:p-8 border border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                        <span class="bg-blue-100 text-blue-600 px-2.5 py-1 rounded-md mr-3 text-xs font-semibold">06</span> 
                        ช่องทางติดต่อช่วยเหลือ (Support Contact)
                    </h2>
                    <p class="text-gray-600 text-sm mb-2">หากพบปัญหาในการใช้งานระบบ สามารถติดต่อผู้ดูแลระบบได้ที่มุมขวาบนของระบบหลักหรือแจ้งผ่านอีเมลภายในองค์กร</p>
                </section>

            </div>
        </div>
    </main>

    <!-- Lightbox: กดที่รูปเพื่อดูภาพใหญ่ (กดพื้นหลัง / ปุ่ม X / Esc เพื่อปิด, ลูกศรซ้ายขวาเพื่อเลื่อนรูปในขั้นตอนเดียวกัน) -->
    <div id="lightbox" aria-hidden="true">
        <button type="button" class="lb-btn lb-close" aria-label="ปิด">&times;</button>
        <button type="button" class="lb-btn lb-prev" aria-label="รูปก่อนหน้า">&#8249;</button>
        <img id="lightbox-img" src="" alt="">
        <button type="button" class="lb-btn lb-next" aria-label="รูปถัดไป">&#8250;</button>
        <div class="lb-caption"></div>
    </div>

    <script>
        (function () {
            const box     = document.getElementById('lightbox');
            const boxImg  = document.getElementById('lightbox-img');
            const caption = box.querySelector('.lb-caption');
            const btnPrev = box.querySelector('.lb-prev');
            const btnNext = box.querySelector('.lb-next');
            let group = [], index = 0;

            function show() {
                const img = group[index];
                boxImg.src = img.src;
                boxImg.alt = img.alt;
                caption.textContent = img.alt + (group.length > 1 ? `  (${index + 1}/${group.length})` : '');
                const multi = group.length > 1;
                btnPrev.style.display = multi ? '' : 'none';
                btnNext.style.display = multi ? '' : 'none';
            }

            function open(img) {
                // รวมรูปในขั้นตอนเดียวกัน (li เดียวกัน) ให้เลื่อนดูต่อได้
                const scope = img.closest('li') || img.closest('section') || document;
                group = Array.from(scope.querySelectorAll('img'));
                index = Math.max(0, group.indexOf(img));
                show();
                box.classList.add('open');
                box.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }

            function close() {
                box.classList.remove('open');
                box.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
                boxImg.src = '';
            }

            function step(d) {
                index = (index + d + group.length) % group.length;
                show();
            }

            document.querySelector('main').addEventListener('click', function (e) {
                const img = e.target.closest('img');
                if (img) open(img);
            });

            box.addEventListener('click', function (e) {
                if (e.target === box || e.target === boxImg || e.target.classList.contains('lb-close')) close();
            });
            btnPrev.addEventListener('click', function (e) { e.stopPropagation(); step(-1); });
            btnNext.addEventListener('click', function (e) { e.stopPropagation(); step(1); });

            document.addEventListener('keydown', function (e) {
                if (!box.classList.contains('open')) return;
                if (e.key === 'Escape') close();
                if (e.key === 'ArrowLeft' && group.length > 1) step(-1);
                if (e.key === 'ArrowRight' && group.length > 1) step(1);
            });
        })();
    </script>

</body>
</html>