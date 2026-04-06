<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class fuel_Logs extends Model
{
    protected $table = 'fuel_logs';

    protected $fillable = [
        'driver_name',
        'vehicle_id',
        'work_date',
        'start_time',
        'end_time',
        'start_mileage',
        'total_distance',
        'liters',
        'total_price',
        'price_per_liter',
        'note'
    ];
    public $timestamps = false;
    protected $casts = [
        'work_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'liters' => 'float',
        'total_distance' => 'float',
        'total_price' => 'float',
        'price_per_liter' => 'float',
    ];
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_at = now();
        });
    }
    protected $appends = [
        'distance',
        'km_per_liter',
        'work_hours'
    ];

    // 🔹 หา log ก่อนหน้า (รถคันเดียวกัน)
    public function previousLog()
    {
        return self::where('vehicle_id', $this->vehicle_id)
            ->where(function ($q) {
                $q->where('work_date', '<', $this->work_date)
                  ->orWhere(function ($q2) {
                      $q2->where('work_date', $this->work_date)
                         ->where('id', '<', $this->id);
                  });
            })
            ->orderBy('work_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();
    }

    // 🔹 ระยะทาง (คำนวณจากไมล์ก่อนหน้า)
    public function getDistanceAttribute()
    {
        $prev = $this->previousLog();

        if ($prev && $this->start_mileage) {
            return $this->start_mileage - $prev->start_mileage;
        }

        return 0;
    }
    public function getKmPerLiterAttribute()
    {
        $dist = ($this->total_distance > 0) ? $this->total_distance : $this->distance;
        if ($this->liters > 0 && $dist > 0) {
            return round($dist / $this->liters, 2);
        }
        return 0;
    }

    // 🔹 ชั่วโมงทำงาน
    public function getWorkHoursAttribute()
    {
        if ($this->start_time && $this->end_time) {
            return round($this->start_time->diffInMinutes($this->end_time) / 60, 2);
        }
        return 0;
    }
}