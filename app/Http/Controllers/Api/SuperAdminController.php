<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function getTenants()
    {
        $tenants = \App\Models\User::where('role', 'manager')
            ->with(['subscriptions.package'])
            ->get();
            
        return response()->json(['data' => $tenants]);
    }

    public function dashboardStats()
    {
        $totalTenants = \App\Models\User::where('role', 'manager')->count();
        
        $activeSubscriptions = \App\Models\Subscription::where('status', 'active')->count();
        $pendingSubscriptions = \App\Models\Subscription::where('status', 'pending')->count();
        
        // Calculate total revenue from active and completed subscriptions
        $totalRevenue = \Illuminate\Support\Facades\DB::table('subscriptions')
            ->join('packages', 'subscriptions.package_id', '=', 'packages.id')
            ->whereIn('subscriptions.status', ['active', 'completed'])
            ->sum('packages.price');

        // Get 5 recent tenants
        $recentTenantsQuery = \App\Models\User::where('role', 'manager')
            ->with(['subscriptions' => function($q) {
                $q->latest()->with('package');
            }])
            ->latest()
            ->take(5)
            ->get();

        $recentTenants = $recentTenantsQuery->map(function($user) {
            $latestSub = $user->subscriptions->first();
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'package' => $latestSub && $latestSub->package ? $latestSub->package->name : 'Tanpa Paket',
                'status' => $latestSub ? $latestSub->status : 'inactive'
            ];
        });

        // Generate monthly chart data (last 6 months)
        $monthly_chart = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthName = $month->translatedFormat('M Y'); // e.g. "Jul 2026"
            
            $revenue = \Illuminate\Support\Facades\DB::table('subscriptions')
                ->join('packages', 'subscriptions.package_id', '=', 'packages.id')
                ->whereIn('subscriptions.status', ['active', 'completed'])
                ->whereMonth('subscriptions.created_at', $month->month)
                ->whereYear('subscriptions.created_at', $month->year)
                ->sum('packages.price');

            $monthly_chart[] = [
                'month' => $monthName,
                'revenue' => $revenue
            ];
        }

        return response()->json([
            'totalTenants' => $totalTenants,
            'activeSubscriptions' => $activeSubscriptions,
            'pendingSubscriptions' => $pendingSubscriptions,
            'totalRevenue' => $totalRevenue,
            'recentTenants' => $recentTenants,
            'monthly_chart' => $monthly_chart
        ]);
    }

    public function getPackages()
    {
        $packages = \App\Models\Package::all();
        return response()->json(['data' => $packages]);
    }

    public function createPackage(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:packages',
            'price' => 'required|numeric',
            'duration_in_days' => 'required|integer',
            'max_outlets' => 'nullable|integer',
            'max_products' => 'nullable|integer',
            'max_users' => 'nullable|integer',
            'has_multi_station' => 'nullable|boolean'
        ]);

        $package = \App\Models\Package::create($request->all());
        return response()->json(['message' => 'Package created', 'data' => $package]);
    }

    public function updatePackage(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string'
        ]);

        $package = \App\Models\Package::findOrFail($id);
        $package->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json(['message' => 'Package updated successfully', 'data' => $package]);
    }

    public function archivePackage($id)
    {
        $package = \App\Models\Package::findOrFail($id);
        
        // Toggle active status (archive / unarchive)
        $package->update([
            'is_active' => !$package->is_active
        ]);

        $status = $package->is_active ? 'diaktifkan' : 'diarsipkan';
        return response()->json(['message' => "Package berhasil $status", 'data' => $package]);
    }

    public function subscribeTenant(Request $request, $managerId)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id'
        ]);

        $manager = \App\Models\User::where('role', 'manager')->findOrFail($managerId);
        $package = \App\Models\Package::findOrFail($request->package_id);

        $subscription = $manager->subscriptions()->create([
            'package_id' => $package->id,
            'start_date' => now(),
            'end_date' => now()->addDays($package->duration_in_days),
            'status' => 'active'
        ]);

        return response()->json(['message' => 'Subscription added successfully', 'data' => $subscription]);
    }

    public function blockTenant($managerId)
    {
        $manager = \App\Models\User::where('role', 'manager')->findOrFail($managerId);
        $manager->update(['is_active' => false]);

        // Revoke all existing auth tokens so they are kicked out immediately
        $manager->tokens()->delete();
        
        try {
            \Illuminate\Support\Facades\Mail::to($manager->email)->send(new \App\Mail\TenantBlockedMail($manager));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send block email: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Tenant berhasil diblokir. Semua akses layanan dihentikan.']);
    }

    public function unblockTenant($managerId)
    {
        $manager = \App\Models\User::where('role', 'manager')->findOrFail($managerId);
        $manager->update(['is_active' => true]);

        return response()->json(['message' => 'Tenant berhasil dibuka blokirnya. Akses layanan dipulihkan.']);
    }
}
