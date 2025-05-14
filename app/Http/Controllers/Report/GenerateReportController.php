<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GenerateReportController extends Controller
{

    function hitungJamKerjaMurni($scan_masuk, $scan_keluar)
    {
        $scan_masuk = strlen($scan_masuk) == 19 ? substr($scan_masuk, -8) : substr($scan_masuk, -5);
        $scan_keluar = strlen($scan_keluar) == 19 ? substr($scan_keluar, -8) : substr($scan_keluar, -5);

        $scan_masuk_time = Carbon::parse($scan_masuk);
        $scan_keluar_time = Carbon::parse($scan_keluar);

        $total_menit_kerja = $scan_masuk_time->diffInMinutes($scan_keluar_time);
        $menit_istirahat = 0;

        $istirahat_times = [
            ['start' => '12:00', 'end' => '12:45'],
            ['start' => '18:00', 'end' => '18:45']
        ];

        foreach ($istirahat_times as $istirahat) {
            $istirahat_start = Carbon::parse($istirahat['start']);
            $istirahat_end = Carbon::parse($istirahat['end']);
            if ($scan_masuk_time < $istirahat_start && $scan_keluar_time > $istirahat_end) {
                $menit_istirahat += $istirahat_start->diffInMinutes($istirahat_end);
            }
        }
        $jam_kerja_murni = $total_menit_kerja - $menit_istirahat;
        return $jam_kerja_murni;
    }

    function hitungJamKerjaEfektif($jam_kerja_murni)
    {
        // Hitung jumlah periode 30 menit
        $periode_30_menit = floor($jam_kerja_murni / 30);

        // Hitung sisa menit
        $sisa_menit = $jam_kerja_murni % 30;

        // Jika sisa menit lebih dari 0, tambahkan satu periode
        if ($sisa_menit > 0) {
            $periode_30_menit++;
        }
    }
    function getJamLemburAkumulasi($jam_lembur_efektif, $lembur, $pangkat_id)
    {
        $jam_lembur_efektif = round($jam_lembur_efektif / 30) * 30;
        $jam_lembur =  floor($jam_lembur_efektif / 60); // Konversi menit ke jam penuh
        $sisa_menit =  $jam_lembur_efektif % 60; // Sisa menit
        $jam_lembur_akumulasi = 0;
        if ($pangkat_id == 1) {
            switch ($lembur) {
                case 'Terusan':
                    $lembur_pertama = min(60, $jam_lembur_efektif);
                    $lembur_selanjutnya = max(0, $jam_lembur_efektif - 60);
                    $jam_lembur_akumulasi = ($lembur_pertama * 1.5) + ($lembur_selanjutnya * 2);
                    return $jam_lembur_akumulasi;

                case 'Lembur Libur':
                    if ($jam_lembur > 8) {
                        $jam_lembur_akumulasi += ($jam_lembur - 8) * 4 * 60; // 9 jam ke atas kali 4
                        $jam_lembur = 8; // Kurangi jam yang sudah dihitung
                    }
                    if ($jam_lembur == 8) {
                        $jam_lembur_akumulasi += 8 * 3 * 60; // 8 jam kali 3
                        $jam_lembur = 7; // Kurangi jam yang sudah dihitung
                    }
                    if ($jam_lembur <= 7) {
                        $jam_lembur_akumulasi += $jam_lembur * 2 * 60; // 1-7 jam kali 2
                    }

                    // Hitung menit yang tersisa (tarif sesuai jam terakhir)
                    if ($jam_lembur_efektif > 480) { // Di atas 8 jam
                        $jam_lembur_akumulasi += $sisa_menit * 4;
                    } elseif ($jam_lembur_efektif > 420) { // 8 jam
                        $jam_lembur_akumulasi += $sisa_menit * 3;
                    } else { // 1-7 jam
                        $jam_lembur_akumulasi += $sisa_menit * 2;
                    }
                    return $jam_lembur_akumulasi;

                default:
                    return $jam_lembur_akumulasi;
            }
        } else {
            return $jam_lembur_efektif;
        }
    }

    function getUangKerajinanOperator($jam_kerja_efektif)
    {
        return $jam_kerja_efektif >= 240 ? 1 : 0;
    }

    function getUangMakanOperator($jam_kerja_efektif)
    {
        return $jam_kerja_efektif >= 240 ? 1 : 0;
    }

    function getUangTransportOperator($jam_kerja_efektif)
    {
        return $jam_kerja_efektif >= 240 ? 1 : 0;
    }

    function getJamLemburTerusan($scan_out, $shift)
    {

        $murni = (int)Carbon::parse($shift->jam_keluar)->diffInMinutes($scan_out) - 15;
        $efektif = 0;
        if ($murni >= 60) {
            $efektif = $murni - ($murni % 30);
        } else {
            $murni = 0;
        }
        return (object) ['murni' => $murni, 'efektif' => $efektif];
    }

    function getJamHilangTerlambat($scan_in, $shift)
    {
        $murni = (int)Carbon::parse($shift->jam_masuk)->diffInMinutes($scan_in);
        $efektif = $murni - ($murni % 15) + (($murni % 15) > 0 ? 15 : 0);
        return (object) compact('murni', 'efektif');
    }

    function getJamHilangPulangCepat($scan_out, $shift)
    {
        $murni = (int)Carbon::parse($scan_out)->diffInMinutes($shift->jam_keluar);
        $efektif = $murni - ($murni % 15) + (($murni % 15) > 0 ? 15 : 0);
        return (object) ['murni' => $murni, 'efektif' => $efektif];
    }

    function getStatusCheckInCheckOut($check, $shift)
    {
        $check_in = $check && $check->in ? (Carbon::parse($check->in)->format('H:i') <= Carbon::parse($shift->jam_masuk)->format('H:i') ? 'Tepat Waktu' : 'Terlambat') : 'Belum Scan Masuk';
        $check_out = $check && $check->out ? (Carbon::parse($check->out)->format('H:i') >= Carbon::parse($shift->jam_keluar)->format('H:i') ? 'Tepat Waktu' : 'Pulang Cepat') : 'Belum Scan Keluar';

        return (object) compact('check_in', 'check_out');
    }

    function getCheckInCheckOutWithoutShift($scan_logs)
    {
        $firstLog = $scan_logs->first();
        $lastLog = $scan_logs->last();

        if ($firstLog && $lastLog) {
            $jamFirst = Carbon::parse($firstLog->scan_date)->format('H');
            $jamLast = Carbon::parse($lastLog->scan_date)->format('H');
            $jarakWaktu = abs(Carbon::parse($lastLog->scan_date)->diffInHours(Carbon::parse($firstLog->scan_date)));

            if ($jarakWaktu <= 1) {
                if ($jamFirst < 12) {
                    $check_in = $firstLog->scan_date;
                    $check_out = null;
                } elseif ($jamLast < 12) {
                    $check_in = null;
                    $check_out = $lastLog->scan_date;
                } else {
                    $check_in = $firstLog->scan_date;
                    $check_out = $lastLog->scan_date;
                }
            } else {
                $check_in = $firstLog->scan_date;
                $check_out = $lastLog->scan_date;
            }
        } else {
            $check_in = null;
            $check_out = null;
        }

        return (object) [
            'in' => $check_in,
            'out' => $check_out
        ];
    }

    function getCheckInCheckOut($scan_logs, $shift)
    {
        // in Minute

        $scan_in = Carbon::parse($shift->jam_masuk);
        $scan_out = Carbon::parse($shift->jam_keluar);
        $result = [];

        $result['in'] = null;
        $result['out'] = null;

        $closest_scan_in = $scan_logs->map(function ($attendance) use ($scan_in, $shift) {
            $scan_time = Carbon::parse($attendance->scan_date);
            $difference = abs($scan_time->diffInMinutes($scan_in, false));
            return $difference > $shift->total_menit_kerja ? null : [
                'scan_date' => $attendance->scan_date,
                'difference' => $difference
            ];
        })->filter()->sortBy('difference')->first();

        $closest_scan_out_before = $scan_logs->filter(function ($attendance) use ($scan_out, $shift) {
            $scan_time = Carbon::parse($attendance->scan_date);
            return $scan_time->lt($scan_out);
        })->map(function ($attendance) use ($scan_out, $shift) {
            $scan_time = Carbon::parse($attendance->scan_date);
            $difference = $scan_time->diffInMinutes($scan_out, true);
            return $difference > $shift->total_menit_kerja ? null : [
                'scan_date' => $attendance->scan_date,
                'difference' => $difference
            ];
        })->filter()->sortBy('difference')->first();

        $farthest_scan_out_after = $scan_logs->filter(function ($attendance) use ($scan_out) {
            $scan_time = Carbon::parse($attendance->scan_date);
            return $scan_time->gt($scan_out);
        })->map(function ($attendance) use ($scan_out) {
            $scan_time = Carbon::parse($attendance->scan_date);
            return [
                'scan_date' => $attendance->scan_date,
                'difference' => $scan_out->diffInMinutes($scan_time, true)
            ];
        })->sortByDesc('difference')->first();


        if ($closest_scan_in) {
            $result['in'] = $closest_scan_in['scan_date'];
        }

        if ($closest_scan_out_before) {
            $result['out'] = $closest_scan_out_before['scan_date'];
        }
        if ($farthest_scan_out_after) {
            $result['out'] = $farthest_scan_out_after['scan_date'];
        }
        if ($scan_logs->where('jenis', 'scan_masuk')->first()) {
            $result['in'] = $scan_logs->where('jenis', 'scan_masuk')->first()->scan_date;
        }
        if ($scan_logs->where('jenis', 'scan_keluar')->first()) {
            $result['out'] = $scan_logs->where('jenis', 'scan_keluar')->first()->scan_date;
        }

        return (object)$result;
    }

    function getEffectiveScanOutTime($scan_out, $check_out)
    {
        $total_minutes = Carbon::parse($scan_out)->diffInMinutes($check_out);
        $adjusted_minutes = intdiv($total_minutes, 60) * 60;
        $remaining_minutes = $total_minutes % 60;

        if ($remaining_minutes > 0 && $remaining_minutes < 15) {
            $remaining_minutes = 15;
        } elseif ($remaining_minutes >= 55) {
            $remaining_minutes = 60;
        } else {
            $remaining_minutes = ceil($remaining_minutes / 15) * 15;
        }

        $total_minutes = $adjusted_minutes + $remaining_minutes;
        if ($remaining_minutes < 0) {
            $total_minutes = $adjusted_minutes - $remaining_minutes;
        }

        // dd($scan_out, $check_out, $total_minutes, $adjusted_minutes, $remaining_minutes);

        return Carbon::parse($scan_out)->subMinutes($total_minutes)->subMinute()->format('Y-m-d H:i:s');
    }

    function getEffectiveScanInTime($scan_in, $check_in)
    {
        $total_minutes = Carbon::parse($scan_in)->diffInMinutes($check_in);
        $adjusted_minutes = intdiv($total_minutes, 60) * 60;
        $remaining_minutes = $total_minutes % 60;

        if ($remaining_minutes > 0 && $remaining_minutes < 15) {
            $remaining_minutes = 15;
        } elseif ($remaining_minutes >= 55) {
            $remaining_minutes = 60;
        } else {
            $remaining_minutes = floor($remaining_minutes / 15) * 15;
        }

        $total_minutes = $adjusted_minutes + $remaining_minutes;

        return Carbon::parse($scan_in)->addMinutes($total_minutes)->format('Y-m-d H:i:s');
    }
}
