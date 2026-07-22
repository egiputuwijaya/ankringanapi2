<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if ($user && !$user->isDeveloper()) {
            // Dapatkan pemilik franchise (manager) dari user ini
            $owner = $user->isManager() ? $user : $user->owner;
            
            if ($owner) {
                // Ambil langganan terbaru
                $latestSub = $owner->subscriptions()->orderBy('end_date', 'desc')->first();
                
                if (!$latestSub || $latestSub->status !== 'active' || $latestSub->end_date < now()) {
                    return response()->json([
                        'message' => 'Masa berlaku langganan telah habis. Silakan perbarui paket Anda.',
                        'error_code' => 'SUBSCRIPTION_EXPIRED'
                    ], 402); // 402 Payment Required
                }
            }
        }

        return $next($request);
    }
}
