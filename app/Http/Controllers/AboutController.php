<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AboutController extends Controller
{
    /**
     * Display the platform overview page.
     */
    public function index(): View
    {
        return view('about');
    }
}
