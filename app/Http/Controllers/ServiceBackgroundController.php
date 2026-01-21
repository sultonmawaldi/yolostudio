<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceBackgroundController extends Controller
{
    public function index($serviceId)
    {
        $service = Service::with(['backgrounds' => function ($q) {
            $q->where('is_active', true)
                ->orderBy('sort_order');
        }])->findOrFail($serviceId);

        return response()->json([
            'backgrounds' => $service->backgrounds->map(function ($bg) {
                return [
                    'id'    => $bg->id,
                    'name'  => $bg->name,
                    'value' => $bg->value, // 🎨 warna hex
                ];
            })
        ]);
    }
}
