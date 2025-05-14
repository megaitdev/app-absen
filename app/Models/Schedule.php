<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
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


    public function shift($date)
    {
        $shifts = explode(',', $this->shifts);
        $dayNumber = Carbon::parse($date)->dayOfWeekIso - 1;
        $shift = Shift::find($shifts[$dayNumber] ?? null);
        if ($shift) {
            $shift->jam_masuk = $date . ' ' . $shift->jam_masuk;
            $shift->jam_keluar = $date . ' ' . $shift->jam_keluar;
            $shift->jam_mulai_istirahat = $date . ' ' . $shift->jam_mulai_istirahat;
            $shift->jam_selesai_istirahat = $date . ' ' . $shift->jam_selesai_istirahat;
        }
        return $shift;
    }

    public function listShift(string $string1, string $string2): array
    {
        // Split the strings into arrays
        $array1 = explode(',', $string1);
        $array2 = explode(',', $string2);

        // Merge the arrays
        $merged = array_merge($array1, $array2);

        // Filter to keep only integer values
        $integers = array_filter($merged, function ($value) {
            return is_numeric($value) && ctype_digit(strval($value));
        });

        // Convert string numbers to integers
        $integers = array_map('intval', $integers);

        // Remove duplicates
        $unique = array_unique($integers);

        // Reset array keys
        return array_values($unique);
    }

    // Relasi dengan DasarJadwal (satu schedule bisa digunakan di banyak dasar jadwal)
    public function dasarJadwals()
    {
        return $this->hasMany(DasarJadwal::class);
    }

    // Relasi dengan Shift untuk setiap hari
    public function shiftSenin()
    {
        return $this->belongsTo(Shift::class, 'senin');
    }

    public function shiftSelasa()
    {
        return $this->belongsTo(Shift::class, 'selasa');
    }

    public function shiftRabu()
    {
        return $this->belongsTo(Shift::class, 'rabu');
    }

    public function shiftKamis()
    {
        return $this->belongsTo(Shift::class, 'kamis');
    }

    public function shiftJumat()
    {
        return $this->belongsTo(Shift::class, 'jumat');
    }

    public function shiftSabtu()
    {
        return $this->belongsTo(Shift::class, 'sabtu');
    }

    public function shiftMinggu()
    {
        return $this->belongsTo(Shift::class, 'minggu');
    }
}
