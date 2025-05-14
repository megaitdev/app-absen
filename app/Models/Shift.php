<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function addDateToTimeFields(?string $date = null): object
    {
        // If no date is provided, use current date
        if ($date === null) {
            $date = Carbon::now();
        }

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
            if (isset($shiftData[$field]) && !empty($shiftData[$field])) {
                // Convert time string to datetime by adding date
                $shiftData[$field] = $date . ' ' . $shiftData[$field];
            }
        }

        return (object) $shiftData;
    }
}
