<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class HomeController extends Controller
{
    public function index()
    {
        $services = Service::where('status', 1)
            ->get(['id', 'title', 'image', 'slug', 'excerpt', 'other']);

        return view('frontend.home', compact('services'));
    }
}
