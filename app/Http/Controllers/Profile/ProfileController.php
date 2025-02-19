<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Resource\CssController;
use App\Http\Controllers\Resource\ScriptController;
use App\Models\User;
use App\Models\Verifikasi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    private $script;
    private $css;
    public function __construct(ScriptController $script, CssController $css)
    {
        $this->script = $script;
        $this->css = $css;
    }
    public function profile()
    {
        // Create an array with the necessary data for the profile view
        $data = [
            'title' => 'Profile',
            'slug' => 'profile',
            'user' => Auth::user(),
            'scripts' => $this->script->getListScript('profile'),
            'csses' => $this->css->getListCss('profile'),
            'profile_tab' => $this->syncProfileTab(),
        ];

        // Return the profile view with the data
        return view('profile.profile', $data);
    }

    function getValidPhoneNumber($nomor)
    {
        // Trim the input to remove leading and trailing whitespace
        $nomor = trim($nomor);
        // Strip HTML tags from the input
        $nomor = strip_tags($nomor);
        // Remove spaces from the input
        $nomor = str_replace(" ", "", $nomor);
        // Remove '+' from the input
        $nomor = str_replace("+", "", $nomor);
        // Remove '(' and ')' from the input
        $nomor = str_replace("(", "", $nomor);
        $nomor = str_replace(")", "", $nomor);
        // Remove dots from the input
        $nomor = str_replace(".", "", $nomor);

        // Check if the input contains only '+' and digits 0-9
        if (!preg_match('/[^+0-9]/', trim($nomor))) {
            // Check if the first 3 characters of the input are '62'
            if (substr(trim($nomor), 0, 3) == '62') {
                $nomor = trim($nomor);
            }
            // Check if the first character of the input is '0'
            elseif (substr($nomor, 0, 1) == '0') {
                $nomor = '62' . substr($nomor, 1);
            }
        }
        // Return the cleaned and validated phone number
        return $nomor;
    }

    function syncProfileTab()
    {
        // Retrieve the value of 'profile-tab' from the session
        $profile_tab = Session::get('profile-tab');

        // Check if the value of 'profile-tab' is not set
        if (!$profile_tab) {
            // Set the value of 'profile-tab' in the session to 'profile'
            Session::put('profile-tab', 'profile');

            // Retrieve the updated value of 'profile-tab' from the session
            $profile_tab = Session::get('profile-tab');
        }

        // Return the value of 'profile-tab'
        return $profile_tab;
    }

    public function setTabActive($tab)
    {
        // Set the value of 'profile-tab' in the session to the provided $tab value
        Session::put('profile-tab', $tab);

        // Retrieve the value of 'profile-tab' from the session and return it as a JSON response
        return response()->json(Session::get('profile-tab'));
    }

    public function update(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if ($request->username != $user->username) {
            $validated = $this->validate($request, [
                'username' => 'required|unique:users',
            ]);
        }
        if ($request->email != $user->email) {
            $validated = $this->validate($request, [
                'email' => 'required|unique:users',
            ]);
        }
        if ($request->nomor_wa != $user->nomor_wa) {
            $validated = $this->validate($request, [
                'nomor_wa' => 'required|unique:users',
            ]);
        }

        // Save the file
        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $filename = $user->username . '.' . $file->getClientOriginalExtension();
            Storage::putFileAs('img/profile', $file, $filename);
            $user->picture = $filename;
        }

        // Update the user data
        $user->update([
            'nama' => $request->input('nama'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'nomor_wa' => $this->getValidPhoneNumber($request->input('nomor_wa')),
        ]);

        return redirect()->route('profile')->with('status', 'Profil Berhasil Di Update!');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:4', 'confirmed'],
        ]);
        $user = User::find(Auth::user()->id);
        $user->fill([
            'password' => Hash::make($request->password)
        ])->save();

        return back()->with('status', 'Password berhasil Diubah!');
    }
}
