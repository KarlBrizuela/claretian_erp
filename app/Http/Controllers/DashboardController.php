<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Super admins see the main dashboard
        if ($user->isSuperAdmin()) {
            $sidebar = 'super-admin';
            
            // Special handling for Directors who might want the director sidebar
            if ($user->position === 'Director' || $user->division === 'All Divisions') {
                $sidebar = 'director';
            }
            
            // Fetch Dashboard Data
            $totalUsers = \App\Models\User::count();
            $activeUsers = \App\Models\User::where('status', true)->count();
            $pendingApprovals = \App\Models\User::where('status', false)->count(); 
            
            // Divisions (count distinct divisions)
            $divisionCount = \App\Models\User::distinct('division')->count('division'); 
            
            $rolesCount = \App\Models\Role::count();
            
            // Activity Logs - fetch latest
            $allActivityLogs = \App\Models\ActivityLog::with('user')->latest()->take(200)->get();
            $activityLogs = $allActivityLogs->take(8);
            $activityLogsCount = \App\Models\ActivityLog::count();
            
            // Division Summary - Get actual user counts per division
            $divisionSummary = \App\Models\User::selectRaw('division, COUNT(*) as user_count')
                ->whereNotNull('division')
                ->where('division', '!=', '')
                ->groupBy('division')
                ->get()
                ->map(function($item) use ($totalUsers) {
                    $percentage = $totalUsers > 0 ? round(($item->user_count / $totalUsers) * 100) : 0;
                    return [
                        'name' => $item->division,
                        'count' => $item->user_count,
                        'percentage' => $percentage
                    ];
                });
            
            // Module Activity - Get actual activity counts per module
            $moduleActivity = \App\Models\ActivityLog::selectRaw('module, COUNT(*) as action_count')
                ->whereNotNull('module')
                ->where('module', '!=', '')
                ->groupBy('module')
                ->orderBy('action_count', 'desc')
                ->get()
                ->map(function($item) use ($activityLogsCount) {
                    $percentage = $activityLogsCount > 0 ? round(($item->action_count / $activityLogsCount) * 100) : 0;
                    return [
                        'name' => $item->module,
                        'count' => $item->action_count,
                        'percentage' => $percentage
                    ];
                });
            
            // User Activity Trends - Get activity data for the last 7 days
            $userActivityTrends = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dayName = $date->format('D');
                
                // Count distinct active users who logged activities on this day
                $activeUsersCount = \App\Models\ActivityLog::whereDate('created_at', $date->toDateString())
                    ->distinct('user_id')
                    ->count('user_id');
                
                // Count login activities
                $loginCount = \App\Models\ActivityLog::whereDate('created_at', $date->toDateString())
                    ->where('action', 'LIKE', '%login%')
                    ->count();
                
                $userActivityTrends[] = [
                    'day' => $dayName,
                    'activeUsers' => $activeUsersCount,
                    'logins' => $loginCount
                ];
            }
            
            return view('dashboard', [
                'sidebar' => $sidebar,
                'totalUsers' => $totalUsers,
                'activeUsers' => $activeUsers,
                'pendingApprovals' => $pendingApprovals,
                'divisionCount' => $divisionCount,
                'rolesCount' => $rolesCount,
                'activityLogs' => $activityLogs,
                'allActivityLogs' => $allActivityLogs,
                'activityLogsCount' => $activityLogsCount,
                'divisionSummary' => $divisionSummary,
                'moduleActivity' => $moduleActivity,
                'userActivityTrends' => $userActivityTrends
            ]);
        }

        // Redirect users based on their specific permissions
        $firstRoute = $user->getFirstAvailableRoute();
        return redirect()->route($firstRoute);
    }

    public function marketing()
    {
        return view('marketing.dashboard');
    }

    public function adminFinance()
    {
        return view('admin-finance.dashboard');
    }

    public function production()
    {
        return view('production.dashboard', [
            'title' => 'Production Division Dashboard',
            'role' => auth()->user()->position,
            'sidebar' => 'production'
        ]);
    }
}
