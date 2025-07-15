<?php

namespace App\Models\mak_hrd;

use App\Http\Controllers\Controller;
use App\Models\DasarJadwal;
use App\Models\Holiday;
use App\Models\mak_hrd\Unit;
use App\Models\ScanLog;
use App\Models\Schedule;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Employee extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'hrd_employees';
    protected $guarded = ['id'];

    public function posisi()
    {
        return $this->hasOne(Posisi::class, 'employee_id', 'id')->where('status', 1);
    }
    public function unit()
    {
        return $this->hasOneThrough(
            Unit::class,
            Posisi::class,
            'employee_id',
            'id',
            'id',
            'unit_id'
        )->where('hrd_posisis.status', 1);
    }

    public function divisi()
    {
        return $this->hasOneThrough(
            Divisi::class,
            Posisi::class,
            'employee_id',
            'id',
            'id',
            'divisi_id'
        )->where('hrd_posisis.status', 1);
    }

    public function pangkat()
    {
        return $this->hasOne(Pangkat::class, 'id', 'pangkat_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'employee_id', 'id');
    }

    public function scan_logs(): HasMany
    {
        return $this->hasMany(ScanLog::class, 'pin', 'pin');
    }

    // Get picture from user or default
    public function getPictureAttribute()
    {
        if ($this->user && $this->user->picture) {
            return $this->user->picture;
        }
        return 'default-avatar.png';
    }

    public function dasarJadwal()
    {
        return $this->hasMany(DasarJadwal::class, 'employee_id');
    }
    public function dasar_jadwal_active()
    {
        return $this->hasOne(DasarJadwal::class, 'employee_id');
    }
    public function countJadwal()
    {
        return $this->hasMany(DasarJadwal::class, 'employee_id')->count();
    }

    public function shifts($startDate, $endDate,)
    {


        // Initialize result array
        $result = [];

        // Get all employee schedules within the date range
        $dasarJadwal = DasarJadwal::where('employee_id', $this->id)
            ->where(function ($query) use ($startDate, $endDate) {
                // If end date is null, only look at the start date
                if ($endDate === null) {
                    $query->where('start_date', '>=', $startDate)
                        ->orWhere(function ($q) use ($startDate) {
                            $q->where('start_date', '<=', $startDate)
                                ->where(function ($subQ) use ($startDate) {
                                    $subQ->where('end_date', '>=', $startDate)
                                        ->orWhereNull('end_date');
                                });
                        });
                } else {
                    // Check for schedules that overlap with the given date range
                    $query->where(function ($q) use ($startDate, $endDate) {
                        // Start date falls within range
                        $q->whereBetween('start_date', [$startDate, $endDate])
                            // End date falls within range
                            ->orWhereBetween('end_date', [$startDate, $endDate])
                            // Range is completely within a schedule
                            ->orWhere(function ($subQ) use ($startDate, $endDate) {
                                $subQ->where('start_date', '<=', $startDate)
                                    ->where(function ($innerQ) use ($endDate) {
                                        $innerQ->where('end_date', '>=', $endDate)
                                            ->orWhereNull('end_date');
                                    });
                            });
                    });
                }
            })
            ->with([
                'schedule',
                'schedule.shiftSenin',
                'schedule.shiftSelasa',
                'schedule.shiftRabu',
                'schedule.shiftKamis',
                'schedule.shiftJumat',
                'schedule.shiftSabtu',
                'schedule.shiftMinggu'
            ])
            ->get();



        // Use CarbonPeriod to iterate through each day in the date range
        $period = CarbonPeriod::create($startDate, '1 day', $endDate);

        $holidays = Holiday::whereBetween('date', [$startDate, $endDate])->pluck('date')->toArray();

        // Loop through each day in the period
        foreach ($period as $currentDate) {
            $dateString = $currentDate->toDateString();
            // Skip if current date is a holiday
            if (in_array($dateString, $holidays)) {
                continue;
            }
            // For each date, find matching schedule
            foreach ($dasarJadwal as $dJ) {
                // Check if current date falls within the dasarJadwal period
                if ($currentDate->gte($dJ->start_date) && $currentDate->lte($dJ->end_date ?? Carbon::now())) {
                    // Get the day of week (0=Sunday, 1=Monday, etc.)
                    $dayOfWeek = $currentDate->dayOfWeek;

                    // Map the day of week to the column name in the second table
                    $shiftName = [
                        0 => 'shiftMinggu',  // Sunday
                        1 => 'shiftSenin',   // Monday
                        2 => 'shiftSelasa',  // Tuesday
                        3 => 'shiftRabu',    // Wednesday
                        4 => 'shiftKamis',   // Thursday
                        5 => 'shiftJumat',   // Friday
                        6 => 'shiftSabtu'    // Saturday
                    ];

                    $shift = $shiftName[$dayOfWeek];

                    // dump($shift);

                    // Get the shift for this dasarJadwal and day of week
                    $shift = $dJ->schedule[$shiftName[$dayOfWeek]];

                    // If shift is found, set it in the result and break out of the loop
                    if ($shift !== null) {
                        $shiftWithDates = $shift->addDateToTimeFields($currentDate->toDateString());
                        $result[$dateString] = $shiftWithDates;
                        break;
                    }
                }
            }
        }

        return $result;
    }

    public function shiftByDate($date)
    {
        $dasarJadwal = $this->dasarJadwal()
            ->where('start_date', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->where('end_date', '>=', $date)
                    ->orWhereNull('end_date');
            })
            ->first();
        $schedule = $dasarJadwal->schedule;
        return $schedule->shift($date) ?? null;
    }
}
