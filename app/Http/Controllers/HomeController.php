<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Studio;

class HomeController extends Controller
{
    public function index()
    {
        $services = Service::where('status', 1)
            ->get(['id', 'title', 'image', 'slug', 'excerpt', 'other']);

        $studios = Studio::where('status', 1)
            ->latest()
            ->limit(3) // tampilkan 3 studio saja di home
            ->get(['id', 'name', 'address', 'image', 'slug']);

        return view('frontend.home', compact('services', 'studios'));
    }
}
