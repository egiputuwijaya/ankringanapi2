<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class WipeExpiredTenants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:wipe-expired';

    protected $description = 'Wipe tenant data if subscription is expired for more than 6 months';

    public function handle()
    {
        $cutoffDate = now()->subMonths(6);
        
        $expiredManagers = \App\Models\User::where('role', 'manager')
            ->whereHas('subscriptions', function ($query) use ($cutoffDate) {
                $query->where('status', 'expired')
                      ->where('end_date', '<', $cutoffDate);
            })->get();

        foreach ($expiredManagers as $manager) {
            $this->info("Wiping data for manager: {$manager->email}");
            // Karena relasi bisa sangat dalam, hapus manual atau bergantung pada onDelete cascade database
            \App\Models\Outlet::where('owner_id', $manager->id)->delete();
            \App\Models\Category::where('owner_id', $manager->id)->delete();
            \App\Models\Product::where('owner_id', $manager->id)->delete();
            \App\Models\Order::where('owner_id', $manager->id)->delete();
            \App\Models\HistoryTransaction::where('owner_id', $manager->id)->delete();
            \App\Models\Shift::where('owner_id', $manager->id)->delete();
            // Optional: soft delete or hard delete manager user
            // $manager->delete();
        }
        
        $this->info("Expired tenant data cleanup completed.");
    }
}
