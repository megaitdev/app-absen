<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function hasShifts($startDate, $endDate)
    {
        $list_shift = explode(',', $this->shifts);
        array_walk($list_shift, function (&$value, $key) {
            $value = trim($value);
        });
        array_unshift($list_shift, null);
        unset($list_shift[0]);

        $list_shift = array_map(function ($value) {
            $shift = [];
            if ($value != 'libur') {
                $shift = Shift::find($value)->toArray();
            }
            return $shift;
        }, $list_shift);

        $result = [];
        $period = CarbonPeriod::create($startDate, $endDate);
        foreach ($period as $date) {
            $dayNumber = $date->dayOfWeekIso;
            if (!empty($list_shift[$dayNumber])) {
                $shift = $list_shift[$dayNumber];
                $shift['day'] = $date->locale('id')->translatedFormat('l');
                $shift['date'] = $date->format('Y-m-d');
                $shift['jam_masuk'] = $date->format('Y-m-d') . ' ' . $shift['jam_masuk'];
                $shift['jam_keluar'] = $date->format('Y-m-d') . ' ' . $shift['jam_keluar'];
                $shift['jam_mulai_istirahat'] = $date->format('Y-m-d') . ' ' . $shift['jam_mulai_istirahat'];
                $shift['jam_selesai_istirahat'] = $date->format('Y-m-d') . ' ' . $shift['jam_selesai_istirahat'];
                $result[$date->format('Y-m-d')] = (object)$shift;
            }
        }
        return $result;
    }
}
