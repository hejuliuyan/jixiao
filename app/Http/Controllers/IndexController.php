<?php

namespace App\Http\Controllers;

use Auth;

class IndexController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', 'side']);
    }

    public function index()
    {
        return view('home.index');
    }

    public function entry()
    {
        return redirect('index');
    }
}
