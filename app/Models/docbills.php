<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Docbills extends Model
{
    use HasFactory;

    protected $table = 'docbills'; // ชื่อตารางในฐานข้อมูล
    protected $primaryKey = 'doc_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    /**
     * ฟิลด์ที่อนุญาตให้บันทึก/แก้ไขได้ (แก้ Error Mass Assignment)
     */
    protected $fillable = [
        'doc_id',
        'status',
        'statuspdf',
        'statusdeli',
        'id_com',
        'so_id',
        'emp_name',
        'com_name',
        'contact_name',
        'contact_tel',
        'com_address',
        'com_la_long',
        'notes',
        'datestamp',
        'doctype',
        'headcom',
        'solve',
    ];
}