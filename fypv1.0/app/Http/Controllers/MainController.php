<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index()
    {
        return view('main');
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    public function goal()
    {
        return view('goal');
    }
    
    public function milestone()
    {
        return view('milestone');
    }

    public function progressTracking()
    {
        return view('progressTracking');
    }
    
    public function recommendation()
    {
        return view('recommendation');
    }

    public function library()
    {
        return view('library');
    }

    public function community()
    {
        return view('community');
    }
    
    public function system()
    {
        return view('system');
    }
}
