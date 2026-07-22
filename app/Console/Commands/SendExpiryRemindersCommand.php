<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Mail\TenantExpiryReminderMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendExpiryRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:send-expiry-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send expiry reminders to tenants whose packages expire in 3 days or less.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $threeDaysFromNow = $now->copy()->addDays(3);

        // Cari langganan yang masih aktif dan end_date nya antara sekarang sampai 3 hari ke depan
        $expiringSubscriptions = Subscription::with('user')
            ->where('status', 'active')
            ->whereBetween('end_date', [$now, $threeDaysFromNow])
            ->get();

        $count = 0;
        foreach ($expiringSubscriptions as $subscription) {
            $user = $subscription->user;
            if ($user && $user->email) {
                $daysRemaining = Carbon::now()->diffInDays($subscription->end_date, false);
                // Pastikan minimal sisa waktu ditampikan sebagai 1 (hari terakhir), atau 0 jika hari ini.
                // Dalam format diffInDays, bisa jadi 0, 1, 2, atau 3.
                $daysRemaining = max(0, intval($daysRemaining));

                Mail::to($user->email)->send(new TenantExpiryReminderMail($user, $daysRemaining));
                $count++;
            }
        }

        $this->info("Successfully sent {$count} expiry reminder emails.");
        Log::info("SendExpiryRemindersCommand executed. Emails sent: {$count}");
    }
}
