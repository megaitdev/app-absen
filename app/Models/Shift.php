<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'is_break' => 'boolean',
        'is_break_extra' => 'boolean',
        'is_sameday' => 'boolean',
        'is_active' => 'boolean',
        'istirahat' => 'array'
    ];

    public function addDateToTimeFields(?string $date = null): object
    {
        // If no date is provided, use current date
        $date = $date === null ? Carbon::now() : Carbon::parse($date);

        // Get current shift data as array
        $shiftData = $this->toArray();
        $shiftData['date'] = $date;

        // Time fields that need date prefixing
        $timeFields = [
            'jam_masuk',
            'jam_keluar',
            'jam_mulai_istirahat',
            'jam_selesai_istirahat'
        ];

        foreach ($timeFields as $field) {
            if (!empty($shiftData[$field])) {
                $isNextDay = in_array($field, ['jam_keluar']) && $this->is_sameday == 0;
                $currentDate = $isNextDay ? $date->copy()->addDay() : $date->copy();
                $shiftData[$field] = $currentDate->format('Y-m-d') . ' ' . $shiftData[$field];
            }
        }

        return (object) $shiftData;
    }
}
