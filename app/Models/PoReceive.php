<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * ตารางเดียวจบ: po_receives
 * 1 แถว = สินค้า 1 รายการที่กดรับเข้า
 * คอลัมน์: id, po_num, good_name, recv_qty, unit_price,
 *          shelf (ชั้นวาง), photo_path (รูป), received_by (ชื่อผู้บันทึก), received_at (เวลาบันทึก)
 */
class PoReceive extends Model
{
    protected $table = 'po_receives';

    // ไม่มี created_at / updated_at
    public $timestamps = false;

    protected $fillable = [
        'po_num',
        'good_name',
        'recv_qty',
        'unit_price',
        'shelf',
        'photo_path',
        'received_by',
        'received_at',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'recv_qty'    => 'decimal:2',
        'unit_price'  => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static $checked = false;

        if ($checked) {
            return;
        }
        $checked = true;

        try {
            if (!Schema::hasTable('po_receives')) {
                Schema::create('po_receives', function (Blueprint $table) {
                    $table->id();
                    $table->string('po_num', 50)->index();
                    $table->string('good_name', 500)->nullable();
                    $table->decimal('recv_qty', 12, 2)->default(0);
                    $table->decimal('unit_price', 12, 2)->nullable();
                    $table->string('shelf', 100)->nullable();
                    $table->string('photo_path')->nullable();
                    $table->string('received_by')->nullable();
                    $table->timestamp('received_at')->useCurrent();
                });

                return;
            }

            // เติมคอลัมน์ที่ขาด
            $needed = [
                'good_name'   => fn (Blueprint $t) => $t->string('good_name', 500)->nullable(),
                'recv_qty'    => fn (Blueprint $t) => $t->decimal('recv_qty', 12, 2)->default(0),
                'unit_price'  => fn (Blueprint $t) => $t->decimal('unit_price', 12, 2)->nullable(),
                'shelf'       => fn (Blueprint $t) => $t->string('shelf', 100)->nullable(),
                'photo_path'  => fn (Blueprint $t) => $t->string('photo_path')->nullable(),
                'received_by' => fn (Blueprint $t) => $t->string('received_by')->nullable(),
                'received_at' => fn (Blueprint $t) => $t->timestamp('received_at')->nullable()->useCurrent(),
            ];

            foreach ($needed as $column => $definition) {
                if (!Schema::hasColumn('po_receives', $column)) {
                    Schema::table('po_receives', function (Blueprint $table) use ($definition) {
                        $definition($table);
                    });
                }
            }

            // ลบคอลัมน์ที่ไม่ใช้
            $unused = [
                'good_id',
                'po_id',
                'vendor_name',
                'item_count',
                'total_qty',
                'created_at',
                'updated_at',
            ];

            foreach ($unused as $column) {
                if (Schema::hasColumn('po_receives', $column)) {
                    Schema::table('po_receives', function (Blueprint $table) use ($column) {
                        $table->dropColumn($column);
                    });
                }
            }
        } catch (\Exception $e) {
            // ปล่อยผ่าน
        }
    }

    /** คืน URL ของรูปแนบ ถ้ามีไฟล์อยู่จริง */
    public function photoUrl(): ?string
    {
        if ($this->photo_path && Storage::disk('public')->exists($this->photo_path)) {
            return Storage::disk('public')->url($this->photo_path);
        }

        return null;
    }
}