<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * Display the contact support and FAQ page.
     */
    public function index(): View
    {
        return view('contact');
    }
}
