<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function dashboard()
    {
        // Check if current location allows creating slips
        $canCreateSlip = false;
        $currentLocationId = Session::get('location_id');
        
        if ($currentLocationId) {
            $location = Location::find($currentLocationId);
            $canCreateSlip = $location && ($location->create_slip ?? false);
        }
        
        return view('user.dashboard', compact('canCreateSlip'));
    }

    public function incomingTrucks()
    {
        return view('user.incoming-trucks');
    }

    public function outgoingTrucks()
    {
        return view('user.outgoing-trucks');
    }

    public function completedTrucks()
    {
        return view('user.completed-trucks');
    }

    public function reports()
    {
        return view('user.reports');
    }

    public function report()
    {
        return view('user.report');
    }
}
