<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Resource\CssController;
use App\Http\Controllers\Resource\ScriptController;
use App\Mail\EmailVerification;
use App\Models\User;
use App\Models\Verifikasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;

class VerifikasiController extends Controller
{
    private $script;
    private $css;
    public function __construct(ScriptController $script, CssController $css)
    {
        $this->script = $script;
        $this->css = $css;
    }
    function verifikasiNomor(User $user)
    {
        // Create an array with the necessary data for the profile view
        $data = [
            'title' => 'Verification WhatsApp Number',
            'slug' => 'profile',
            'user' => $user,
            'scripts' => $this->script->getListScript('verifikasi-nomor'),
            'csses' => $this->css->getListCss('verifikasi-nomor'),
        ];

        // Return the profile view with the data
        return view('profile.verifikasi-nomor', $data);
    }

    function isVerifiedNomor(User $user)
    {
        if ($user->nomor_wa_verified_at) {
            return response()->json(true);
        }
        return response()->json(false);
    }

    function sendCodeNomor(User $user)
    {
        $verifikasi = Verifikasi::where([['user_id', $user->id], ['type', 'nomor']])->first();
        if ($verifikasi) {
            if ($verifikasi->expired_at > Carbon::now()) {
                return response()->json([
                    'code' => 501,
                    'message' => "Code already sent",
                    'data' => $verifikasi,
                ]);
            } else {
                $verifikasi->code = rand(100000, 999999);
                $verifikasi->expired_at = Carbon::now()->addMinutes(5);
                $verifikasi->save();
                $message = "*WhatsApp Verification Code*\n\nYour WhatsApp verification code is: [{$verifikasi->code}]\n\nPlease enter this code in the app to complete your verification. Do not share this code with anyone for your security.\n\n\nSent from Megacan";
                $this->megacanSendMessage($user->nomor_wa, $message);
                return response()->json([
                    'code' => 200,
                    'message' => "Code succesfully sent!",
                    'data' => $verifikasi,
                ]);
            }
        } else {
            $verifikasi = Verifikasi::create(
                [
                    'user_id' => $user->id,
                    'nomor_wa' => $user->nomor_wa,
                    'type' => 'nomor',
                    'code' => rand(100000, 999999),
                    'expired_at' => Carbon::now()->addMinutes(5),
                ]
            );
            $message = "*WhatsApp Verification Code*\n\nYour WhatsApp verification code is: [{$verifikasi->code}]\n\nPlease enter this code in the app to complete your verification. Do not share this code with anyone for your security.\n\n\nSent from Megacan";
            $this->megacanSendMessage($user->nomor_wa, $message);
            return response()->json([
                'code' => 200,
                'message' => "Code succesfully sent!",
                'data' => $verifikasi,
            ]);
        }
    }

    function resendCodeNomor(User $user)
    {
        $verifikasi = Verifikasi::where([['user_id', $user->id], ['type', 'nomor']])->first();
        if ($verifikasi) {
            if ($verifikasi->expired_at > Carbon::now()) {
                $message = "*WhatsApp Verification Code*\n\nYour WhatsApp verification code is: [{$verifikasi->code}]\n\nPlease enter this code in the app to complete your verification. Do not share this code with anyone for your security.\n\n\nSent from Megacan";
                $this->megacanSendMessage($user->nomor_wa, $message);
                return response()->json([
                    'code' => 501,
                    'message' => "Code already sent",
                    'data' => $verifikasi,
                ]);
            } else {
                $verifikasi->code = rand(100000, 999999);
                $verifikasi->expired_at = Carbon::now()->addMinutes(5);
                $verifikasi->save();
                $message = "*WhatsApp Verification Code*\n\nYour WhatsApp verification code is: [{$verifikasi->code}]\n\nPlease enter this code in the app to complete your verification. Do not share this code with anyone for your security.\n\n\nSent from Megacan";
                $this->megacanSendMessage($user->nomor_wa, $message);
                return response()->json([
                    'code' => 200,
                    'message' => "Code succesfully sent!",
                    'data' => $verifikasi,
                ]);
            }
        }
    }

    function verifiedNomor(User $user)
    {
        $user->nomor_wa_verified_at = Carbon::now();
        $user->save();
        return response()->json([
            'code' => 200,
            'message' => "Number Verified Successfully",
            'data' => $user,
        ]);
    }


    // Email Verification Section
    function verifikasiEmail(User $user)
    {
        // Create an array with the necessary data for the profile view
        $data = [
            'title' => 'Verification Email',
            'slug' => 'profile',
            'user' => $user,
            'scripts' => $this->script->getListScript('verifikasi-email'),
            'csses' => $this->css->getListCss('verifikasi-email'),
        ];

        // Return the profile view with the data
        return view('profile.verifikasi-email', $data);
    }

    function isVerifiedEmail(User $user)
    {
        if ($user->email_verified_at) {
            return response()->json(true);
        }
        return response()->json(false);
    }

    function sendCodeEmail(User $user)
    {
        $verifikasi = Verifikasi::where([['user_id', $user->id], ['type', 'email']])->first();
        if ($verifikasi) {
            if ($verifikasi->expired_at > Carbon::now()) {
                return response()->json([
                    'code' => 501,
                    'message' => "Code already sent",
                    'data' => $verifikasi,
                ]);
            } else {
                $verifikasi->code = rand(100000, 999999);
                $verifikasi->expired_at = Carbon::now()->addMinutes(5);
                $verifikasi->save();
                Mail::to($user->email)->send(new EmailVerification($user->username, $verifikasi->code));
                return response()->json([
                    'code' => 200,
                    'message' => "Code succesfully sent!",
                    'data' => $verifikasi,
                ]);
            }
        } else {
            $verifikasi = Verifikasi::create(
                [
                    'user_id' => $user->id,
                    'nomor_wa' => $user->nomor_wa,
                    'type' => 'email',
                    'code' => rand(100000, 999999),
                    'expired_at' => Carbon::now()->addMinutes(5),
                ]
            );
            Mail::to($user->email)->send(new EmailVerification($user->username, $verifikasi->code));
            return response()->json([
                'code' => 200,
                'message' => "Code succesfully sent!",
                'data' => $verifikasi,
            ]);
        }
    }

    function resendCodeEmail(User $user)
    {
        $verifikasi = Verifikasi::where([['user_id', $user->id], ['type', 'email']])->first();
        if ($verifikasi) {
            if ($verifikasi->expired_at > Carbon::now()) {
                Mail::to($user->email)->send(new EmailVerification($user->username, $verifikasi->code));
                return response()->json([
                    'code' => 501,
                    'message' => "Code already sent",
                    'data' => $verifikasi,
                ]);
            } else {
                $verifikasi->code = rand(100000, 999999);
                $verifikasi->expired_at = Carbon::now()->addMinutes(5);
                $verifikasi->save();
                Mail::to($user->email)->send(new EmailVerification($user->username, $verifikasi->code));
                return response()->json([
                    'code' => 200,
                    'message' => "Code succesfully sent!",
                    'data' => $verifikasi,
                ]);
            }
        }
    }

    function verifiedEmail(User $user)
    {
        $user->email_verified_at = Carbon::now();
        $user->save();
        return response()->json([
            'code' => 200,
            'message' => "Email Verified Successfully",
            'data' => $user,
        ]);
    }
}
