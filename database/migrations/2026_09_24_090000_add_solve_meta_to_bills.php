<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * เก็บ "ใคร/เมื่อไหร่" ที่แก้ปัญหางานของผิด/ค้างบิล (ใช้ในหน้า /wrongbill)
 *   - solve_by : ชื่อผู้แก้ (Sale/ผู้ดูแล)
 *   - solve_at : เวลาที่แก้
 * (คอลัมน์ solve เดิมเก็บ "วิธีแก้" อยู่แล้ว)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tblbill', function (Blueprint $table) {
            if (!Schema::hasColumn('tblbill', 'solve_by'))  $table->string('solve_by')->nullable();
            if (!Schema::hasColumn('tblbill', 'solve_at'))  $table->dateTime('solve_at')->nullable();
        });
        Schema::table('docbills', function (Blueprint $table) {
            if (!Schema::hasColumn('docbills', 'solve_by')) $table->string('solve_by')->nullable();
            if (!Schema::hasColumn('docbills', 'solve_at')) $table->dateTime('solve_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tblbill', function (Blueprint $table) {
            $table->dropColumn(['solve_by', 'solve_at']);
        });
        Schema::table('docbills', function (Blueprint $table) {
            $table->dropColumn(['solve_by', 'solve_at']);
        });
    }
};
