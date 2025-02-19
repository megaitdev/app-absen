<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use InvalidArgumentException;
use RuntimeException;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    /**
     * Get the start and end dates for a given month.
     *
     * @param int $month The month to get the period for (1-12)
     * @param int|null $year The year to get the period for (optional, defaults to current year)
     * @return array An array containing the start and end dates in 'Y-m-d' format
     * @throws InvalidArgumentException If the month is invalid
     */
    public function getPeriodForMonth(int $month, ?int $year = null): array
    {
        if ($month < 1 || $month > 12) {
            throw new InvalidArgumentException('Month must be between 1 and 12');
        }

        $year = $year ?? date('Y');

        try {
            $start = Carbon::createFromDate($year, $month, 21)->subMonth()->format('Y-m-d');
            $end = Carbon::createFromDate($year, $month, 20)->format('Y-m-d');
        } catch (Exception $e) {
            throw new RuntimeException('Error generating period: ' . $e->getMessage());
        }

        return compact('start', 'end');
    }
    // This function converts minutes to effective hours
    function menitToEfektifJam($menit)
    {
        // Set the duration of each effective minute
        $efektivitasMenit = 15;

        // Calculate the divisor to convert minutes to effective minutes
        $pembagi =  100 / (60 / $efektivitasMenit) / 100;

        // Calculate the remaining minutes after converting to hours
        $sisaMenit = $menit % 60;

        // Convert the remaining minutes to effective minutes using the divisor
        $sisaMenit = floor($sisaMenit / $efektivitasMenit) * $pembagi;

        // Calculate the number of hours by dividing the total minutes by 60 and adding the effective minutes
        $jam = floor($menit / 60) + $sisaMenit;

        // Return the result in hours
        return $jam;

        // Example usage:
        // input: 90 minutes
        // output: 1.5 hours
    }
    // This function converts minutes to actual hours
    function menitToAktualJam($menit)
    {
        // Divide the number of minutes by 60 to get the actual hours
        $aktualJam = $menit / 60;

        // Format the actual hours as a float with 2 decimal places
        $formattedJam = number_format($aktualJam, 2, '.', ',');

        // Convert the formatted string back to a float
        $finalJam = floatval($formattedJam);

        // Return the result as the actual hours
        return $finalJam;
    }

    /**
     * This function sends a message using the Megacan API.
     *
     * @param string $nomor_wa The WhatsApp number to send the message to.
     * @param string $message The content of the message to be sent.
     * @return object The response object from the Megacan API.
     */
    function megacanSendMessage($nomor_wa, $message)
    {
        // Get the Megacan API URL from the environment variable
        $url_megacan = config('services.megacan.url');

        // Prepare the data payload for the API request
        $data = [
            'chatId' => $nomor_wa . '@c.us',
            "contentType" => "string",
            "content" => $message
        ];

        // Send the API request using the HTTP POST method
        $response = Http::withBody(json_encode($data), 'application/json')
            ->post($url_megacan . '/client/sendMessage/megacan');

        // Return the response object from the Megacan API
        return $response->object();
    }


    /**
     * This function extracts a date from a string and returns it as a Carbon object or a string.
     *
     * @param string $stringTanggal The string containing the date.
     * @param string $tag The tag indicating which part of the date to extract ('mulai' or 'selesai').
     * @param string $type The type of the return value ('carbon' or 'string'). Default is 'carbon'.
     * @return Carbon|string The extracted date as a Carbon object or a string, depending on the $type parameter.
     */
    public function getTanggalFromString($stringTanggal, $tag, $type = 'carbon')
    {
        // Split the stringTanggal into an array using the "-" delimiter
        $tanggal = explode("-", $stringTanggal);

        // Initialize the $result variable to null
        $result = null;

        // Use a switch statement to handle different $tag values
        switch ($tag) {
            case 'mulai':
                // If $tag is 'mulai', assign the first element of $tanggal to $result
                $result = $tanggal[0];
                break;
            case 'selesai':
                // If $tag is 'selesai', assign the second element of $tanggal to $result
                $result = $tanggal[1];
                break;
        }

        // Check the $type parameter to determine the return value
        if ($type == 'carbon') {
            // If $type is 'carbon', parse $result as a Carbon object and return it
            return Carbon::parse($result);
        }

        // If $type is 'string', return $result as a string
        return $result;
    }
}
