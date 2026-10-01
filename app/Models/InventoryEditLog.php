<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryEditLog extends Model
{
    protected $table = 'inventory_edit_logs';
    public $timestamps = false;   // ใช้ created_at อย่างเดียว (useCurrent)

    protected $fillable = [
        'action', 'target_type', 'target_id', 'target_name',
        'changes', 'qty_from', 'qty_to', 'reason',
        'edited_by', 'edited_by_id', 'role', 'created_at',
    ];
}
