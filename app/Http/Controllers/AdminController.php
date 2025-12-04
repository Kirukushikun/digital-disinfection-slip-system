<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function guards()
    {
        return view('admin.guards');
    }
    public function drivers()
    {
        return view('admin.drivers');
    }
    public function locations()
    {
        return view('admin.locations');
    }
    public function plateNumbers()
    {
        return view('admin.plate-numbers');
    }
    public function trucks()
    {
        return view('admin.trucks');
    }
}
