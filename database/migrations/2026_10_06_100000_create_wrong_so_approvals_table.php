<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * อนุมัติ "ของผิด" ราย SO — admin/accounting กดอนุมัติในหน้า dashboardwrong
 *   ถ้ายังไม่อนุมัติ หน้า so/show (server_update) จะไม่ให้บันทึกข้อมูลจัดส่ง
 *   เปิด/ปิด (toggle) ได้ ; เก็บเวลา+ชื่อผู้อนุมัติ
 *   อยู่ฐาน logistic (server_update อ่านผ่าน mysql_VIRTUAL1)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wrong_so_approvals', function (Blueprint $t) {
            $t->id();
            $t->string('so_id', 50)->unique();
            $t->boolean('approved')->default(false);
            $t->string('approved_by', 100)->nullable();
            $t->timestamp('approved_at')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wrong_so_approvals');
    }
};
