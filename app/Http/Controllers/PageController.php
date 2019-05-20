<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;

class PageController extends Controller
{
    public function index()
    {
        return view('index');
    }
}
