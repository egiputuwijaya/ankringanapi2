<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureManagerIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // 1. Jika User Authenticated (Aplikasi POS)
        if ($user) {
            // Jika dia manager, pastikan akunnya sendiri aktif
            if ($user->role === 'manager' && !$user->is_active) {
                return response()->json(['message' => 'Akun franchise Anda telah disuspend oleh Super Admin. Hubungi dukungan.'], 403);
            }

            // Jika dia karyawan, pastikan akun karyawan aktif, DAN akun bos-nya (Manager) juga aktif
            if ($user->role === 'karyawan') {
                if (!$user->is_active) {
                    return response()->json(['message' => 'Akun karyawan Anda tidak aktif.'], 403);
                }

                $manager = \App\Models\Outlet::where('id', $user->outlet_id)->with('owner')->first()?->owner;
                if ($manager && !$manager->is_active) {
                    return response()->json(['message' => 'Layanan ditangguhkan. Akun pusat (Manager) sedang disuspend.'], 403);
                }
            }
        } 
        // 2. Jika Request dari Public QR (Pelanggan)
        else {
            // Biasanya request public bawa parameter outlet_id atau order punya outlet_id
            // Untuk public menu, pakai parameter 'token' (QR Token meja)
            $token = $request->route('token');
            if ($token) {
                $table = \App\Models\Table::where('qr_token', $token)->first();
                if ($table) {
                    $manager = \App\Models\Outlet::where('id', $table->outlet_id)->with('owner')->first()?->owner;
                    if ($manager && !$manager->is_active) {
                        return response()->json(['message' => 'Toko sedang tidak tersedia saat ini (Suspended).'], 403);
                    }
                }
            }
            
            // Untuk order public yang menerima 'outlet_id'
            $outletId = $request->input('outlet_id') ?? $request->query('outlet_id');
            if ($outletId) {
                $manager = \App\Models\Outlet::where('id', $outletId)->with('owner')->first()?->owner;
                if ($manager && !$manager->is_active) {
                    return response()->json(['message' => 'Toko sedang tidak tersedia saat ini (Suspended).'], 403);
                }
            }
        }

        return $next($request);
    }
}
