<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormResponse;
use App\Models\User;
use App\Models\Precinto;
use App\Models\FormVersion;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_inspections' => FormResponse::count(),
            'completed_inspections' => FormResponse::where('status', 'completed')->count(),
            'pending_inspections' => FormResponse::where('status', 'pending_monitoreo')->count(),
            'rejected_inspections' => FormResponse::where('status', 'rejected')->count(),
            'total_users' => User::count(),
            'active_forms' => FormVersion::where('is_active', true)->count(),
            'total_precintos' => Precinto::count(),
            'available_precintos' => Precinto::where('estado', 'disponible')->count(),
        ];

        $recent_inspections = FormResponse::with(['user', 'formVersion'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $users_by_role = User::select('role', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('role')
            ->get();

        return view('admin.index', compact('stats', 'recent_inspections', 'users_by_role'));
    }
}
