<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\Location;

class SessionController extends Controller
{
    public function create(Request $request, $location = null)
    {
        // If user is logged in, redirect to /home
        if (Auth::check()) {
            return redirect('/');
        }
        
        $locationModel = null;
        if ($location) {
            $locationModel = Location::findOrFail($location);
        }
        
        return view('auth.login', [
            'location' => $locationModel
        ]);
    }

    public function store(Request $request, $location = null){
        $attributes = request()->validate([
            "username"=> ['required'],
            "password"=> ['required'],
        ]);

        if(! Auth::attempt($attributes)){
            throw ValidationException::withMessages([
                'username'=> 'Sorry, those credentials are incorrect',
                'password'=> '',
            ]);
        }
        
        request()->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // If no location is provided, only allow admin (1) or superadmin (2) to login
        // Regular users (0) must login through a location
        if (!$location && $user->user_type === 0) {
            Auth::logout();
            throw ValidationException::withMessages([
                'username' => 'Regular users must login through a location',
                'password' => '',
            ]);
        }

        // Store location in session if provided
        if ($location) {
            $locationModel = Location::findOrFail($location);
            $request->session()->put('location_id', $locationModel->id);
            $request->session()->put('location_name', $locationModel->location_name);
        }

        return redirect()->route($user->dashboardRoute());
    }

    public function destroy()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    }
}
