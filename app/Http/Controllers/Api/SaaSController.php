<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaaSController extends Controller
{
    public function registerManager(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'phone_number' => 'nullable|string|max:30',
            'outlet_name' => 'required|string|max:100',
            'package_id' => 'required|exists:packages,id'
        ]);

        $package = \App\Models\Package::findOrFail($request->package_id);

        DB::beginTransaction();
        try {
            // 1. Create User Manager
            $user = new \App\Models\User();
            $user->name = $request->name;
            $user->email = strtolower($request->email);
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
            $user->phone_number = $request->phone_number;
            $user->role = 'manager';
            $user->save();

            // 2. Create Default Outlet
            $outlet = \App\Models\Outlet::create([
                'name' => $request->outlet_name,
                'owner_id' => $user->id,
            ]);

            $user->update(['outlet_id' => $outlet->id]);

            // 3. Create Pending Subscription
            $orderId = 'SAAS-' . \Illuminate\Support\Str::uuid()->toString();

            $subscription = $user->subscriptions()->create([
                'package_id' => $package->id,
                'midtrans_order_id' => $orderId,
                'status' => 'pending',
                // start_date and end_date are null for now
            ]);

            // 4. Generate Midtrans Snap Token
            // Use Developer/SuperAdmin's global Midtrans Key
            \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $package->price,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone_number,
                ],
                'item_details' => [
                    [
                        'id' => 'PKG-' . $package->id,
                        'price' => (int) $package->price,
                        'quantity' => 1,
                        'name' => 'SaaS Subscription: ' . $package->name,
                    ]
                ]
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);

            $subscription->update(['snap_token' => $snapToken]);

            // Issue login token directly so they don't have to login again
            $token = $user->createToken('auth_token', ['*'])->plainTextToken;

            DB::commit();

            // Send Invoice Email
            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\InvoiceMail($user, $package));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send invoice email: ' . $e->getMessage());
            }

            return response()->json([
                'message' => 'Registrasi berhasil, silakan selesaikan pembayaran langganan',
                'token' => $token,
                'user' => $user,
                'snap_token' => $snapToken,
                'subscription_id' => $subscription->id,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Registrasi gagal: ' . $e->getMessage()], 500);
        }
    }

    public function midtransCallback(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        
        if ($hashed == $request->signature_key) {
            if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                $subscription = \App\Models\Subscription::where('midtrans_order_id', $request->order_id)->first();
                if ($subscription && $subscription->status == 'pending') {
                    $package = \App\Models\Package::find($subscription->package_id);
                    $subscription->update([
                        'status' => 'active',
                        'start_date' => now(),
                        'end_date' => now()->addDays($package->duration_in_days),
                    ]);
                    
                    try {
                        $user = $subscription->user;
                        if ($user && $user->email) {
                            $paymentType = $request->payment_type ?? 'unknown';
                            $grossAmount = $request->gross_amount ?? $package->price;
                            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\TenantActivatedMail($user, $package, $paymentType, $grossAmount));
                        }
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to send activation email: ' . $e->getMessage());
                    }
                }
            }
            return response()->json(['message' => 'Callback processed']);
        }

        return response()->json(['message' => 'Invalid signature'], 403);
    }}
