<?php

namespace App\Http\Controllers;

use App\Models\Observation;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function index(Request $request): Response
    {
        // Verify user is admin
        abort_unless($request->user()?->isAdmin(), 403);

        // Get all users with their credit balances and observation counts
        $users = User::withCount('observations')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_admin' => $user->is_admin,
                    'credits' => $user->creditBalance(),
                    'observations_count' => $user->observations_count,
                    'created_at' => $user->created_at->format('M d, Y'),
                ];
            });

        // Get total metrics
        $totalUsers = User::count();
        $totalObservations = Observation::count();
        $totalCreditsUsed = DB::table('credits')
            ->where('amount', '<', 0)
            ->sum(DB::raw('ABS(amount)'));

        // Get recent observations across all users
        $recentObservations = Observation::with('user:id,name')
            ->latest()
            ->limit(10)
            ->get(['id', 'user_id', 'title', 'status', 'created_at'])
            ->map(fn ($obs) => [
                'id' => $obs->id,
                'user_name' => $obs->user->name,
                'title' => $obs->title,
                'status' => $obs->status,
                'created_at' => $obs->created_at->diffForHumans(),
            ]);

        // Get all packages
        $packages = Package::ordered()->get(['id', 'name', 'credits', 'price', 'is_active', 'is_popular', 'sort_order']);

        return Inertia::render('admin/Dashboard', [
            'users' => $users,
            'metrics' => [
                'totalUsers' => $totalUsers,
                'totalObservations' => $totalObservations,
                'totalCreditsUsed' => $totalCreditsUsed,
            ],
            'recentObservations' => $recentObservations,
            'packages' => $packages,
        ]);
    }
}
