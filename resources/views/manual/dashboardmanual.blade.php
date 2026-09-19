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
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased" x-data="{ activeTab: null }">

    <main class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8 space-y-8">
        
        <div class="text-center bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <h1 class="text-2xl font-bold text-gray-800 flex items-center justify-center">
                <span class="mr-2 text-2xl">📖</span> คู่มือการใช้งานระบบ (Dashboard Manual)
            </h1>
            <p class="text-gray-500 text-sm mt-1">รายละเอียดและขั้นตอนการใช้งานฟังก์ชันต่างๆ ภายในระบบบริหารจัดการ</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
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
                            <button @click="activeTab = 'create-so'" :class="activeTab === 'create-so' ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-600 hover:text-blue-600'" class="w-full text-left px-3 py-2 rounded-md transition">
                                3. การสร้าง SO ใหม่ (Create SO)
                            </button>
                        </li>
                        <li>
                            <button @click="activeTab = 'search-filter'" :class="activeTab === 'search-filter' ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-600 hover:text-blue-600'" class="w-full text-left px-3 py-2 rounded-md transition">
                                4. การค้นหาและกรองข้อมูล
                            </button>
                        </li>
                        <li>
                            <button @click="activeTab = 'table-info'" :class="activeTab === 'table-info' ? 'text-blue-600 font-bold bg-blue-50' : 'text-gray-600 hover:text-blue-600'" class="w-full text-left px-3 py-2 rounded-md transition">
                                5. รายละเอียดตารางและสถานะ
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

            <div class="lg:col-span-3 space-y-6">
                
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
                                
                                <div class="mt-3 mb-2 grid grid-cols-1 sm:grid-cols-2 gap-4 pl-0 sm:pl-4">
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
                                
                                <div class="mt-3 mb-2 grid grid-cols-1 sm:grid-cols-2 gap-4 pl-0 sm:pl-4">
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

                                <div class="mt-3 mb-2 space-y-4 sm:space-y-0 sm:flex sm:space-x-4 pl-0 sm:pl-4">

                                    <div class="border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm sm:w-1/2">
                                        <img src="https://lh3.googleusercontent.com/d/1jr18bqS8O5tBH3eRu96hz8ir_yAYFzuY" alt="ขั้นตอนบันทึกข้อมูลจัดส่ง 1" class="w-full h-48 rounded object-contain bg-gray-100">
                                    </div>

                                    <div class="relative group max-w-lg sm:w-1/2 mx-auto sm:mx-0">
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
                                <div class="mt-3 mb-2 grid grid-cols-1 sm:grid-cols-2 gap-4 pl-0 sm:pl-4">
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

                                <div class="relative group border border-gray-200 rounded-lg p-1 bg-gray-50 shadow-sm overflow-hidden">
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

                <section x-show="activeTab === null || activeTab === 'create-so'" x-transition class="bg-white rounded-xl shadow-sm p-6 sm:p-8 border border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                        <span class="bg-blue-100 text-blue-600 px-2.5 py-1 rounded-md mr-3 text-xs font-semibold">03</span> 
                        การสร้าง SO ใหม่ (Create SO)
                    </h2>
                    <p class="text-gray-600 text-sm mb-4">สำหรับสร้างคำสั่งซื้อใหม่ ท่านสามารถคลิกที่ปุ่มสีเขียวตามตัวอย่างด้านล่าง:</p>
                    <div class="inline-block bg-emerald-700 text-white px-3 py-1.5 rounded-md text-xs font-semibold shadow-sm mb-3">
                         Create SO
                    </div>
                    <ul class="list-disc list-inside space-y-1.5 text-sm text-gray-600">
                        <li>กรอกรายละเอียดข้อมูลลูกค้า รหัสสินค้า และจำนวนให้ครบถ้วน</li>
                        <li>คลิกปุ่มบันทึกข้อมูล (Save) เพื่อยืนยันการสร้าง SO</li>
                    </ul>
                </section>

                <section x-show="activeTab === null || activeTab === 'search-filter'" x-transition class="bg-white rounded-xl shadow-sm p-6 sm:p-8 border border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                        <span class="bg-blue-100 text-blue-600 px-2.5 py-1 rounded-md mr-3 text-xs font-semibold">04</span> 
                        การค้นหาข้อมูล (Searching Data)
                    </h2>
                    <p class="text-gray-600 text-sm mb-4">ท่านสามารถค้นหาข้อมูลย้อนหลังหรือกรองข้อมูลตามเงื่อนไขต่างๆ ได้จากฟิลเตอร์ด้านบน:</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="border border-blue-300 bg-blue-50 text-blue-700 px-3 py-1 rounded text-xs font-medium">🔍 Search (ค้นหา)</span>
                        <span class="border border-red-300 bg-red-50 text-red-600 px-3 py-1 rounded text-xs font-medium">🔄 Reset (ล้างค่า)</span>
                    </div>
                </section>

                <section x-show="activeTab === null || activeTab === 'table-info'" x-transition class="bg-white rounded-xl shadow-sm p-6 sm:p-8 border border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                        <span class="bg-blue-100 text-blue-600 px-2.5 py-1 rounded-md mr-3 text-xs font-semibold">05</span> 
                        ตารางแสดงข้อมูลและสถานะ (Table & Status)
                    </h2>
                    <p class="text-gray-600 text-sm mb-4">ตารางจะแสดงผลรายการ SO, รหัสลูกค้า, ชื่อลูกค้า และสถานะปัจจุบัน (เช่น <span class="text-pink-600 font-semibold">PARTIAL</span> หมายถึง สินค้ายังจัดส่งไม่ครบ)</p>

                    <div class="border border-gray-200 rounded-lg overflow-hidden mt-4">
                        <div class="bg-emerald-100 px-4 py-2 text-xs font-semibold text-emerald-900 border-b border-emerald-200">
                            ข้อมูลทั้งหมด 140160 (ตัวอย่างการแสดงผล)
                        </div>
                        <table class="w-full text-left text-xs">
                            <thead class="bg-emerald-100 text-emerald-900 font-semibold border-b border-emerald-200">
                                <tr>
                                    <th class="p-2.5">SO</th>
                                    <th class="p-2.5">รหัสลูกค้า</th>
                                    <th class="p-2.5">ชื่อลูกค้า</th>
                                    <th class="p-2.5">สถานะ</th>
                                    <th class="p-2.5">วันส่ง</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700">
                                <tr class="hover:bg-gray-50">
                                    <td class="p-2.5 text-pink-600 font-medium">69/014203</td>
                                    <td class="p-2.5 bg-pink-50 text-pink-800">CUS-06003</td>
                                    <td class="p-2.5">แคล-คอมพ์ อีเล็คโทรนิคส์ฯ</td>
                                    <td class="p-2.5">PARTIAL</td>
                                    <td class="p-2.5">2026-07-15</td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="p-2.5 text-pink-600 font-medium">69/014202</td>
                                    <td class="p-2.5 bg-pink-50 text-pink-800">CUS-11315</td>
                                    <td class="p-2.5">เคซีอี เทคโนโลยี จำกัด</td>
                                    <td class="p-2.5">PARTIAL</td>
                                    <td class="p-2.5">2026-08-08</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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

    <footer class="bg-white border-t border-gray-200 mt-12 py-6 text-center text-xs text-gray-500">
        <p>&copy; 2026 Dashboard Manual System. All rights reserved.</p>
    </footer>

</body>
</html>