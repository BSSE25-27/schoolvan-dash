<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Operator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log; // Import the Log facade
use AfricasTalking\SDK\AfricasTalking;
use Exception;
use App\Utilities\PhoneNumberUtility;

class OperatorController extends Controller
{
    public function verifyOtp(Request $request)
    {
        Log::info("Verify OTP Request: " . json_encode($request->all()));

        $validated = $request->validate([
            'phone_number' => 'required|string',
            'otp' => 'required|digits:5',
        ]);

        $originalPhone = $request->phone_number;
        $key = 'otp_' . $originalPhone;
        $storedOtp = Cache::get($key);

        Log::debug("Cache Key: $key");
        Log::debug("Stored OTP: " . json_encode($storedOtp));

        // Debug all cache keys (temporarily)
        if (app()->environment('local')) {
            $allKeys = [];
            if (config('cache.default') == 'file') {
                $storage = storage_path('framework/cache/data');
                $allKeys = glob("$storage/*");
            }
            Log::debug("All cache keys: " . json_encode($allKeys));
        }

        if (!$storedOtp) {
            Log::warning("No OTP found for phone: $originalPhone");
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP code or expired'
            ], 400);
        }

        if ($storedOtp['code'] != $request->otp) {
            Log::warning("OTP mismatch for $originalPhone");
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP code',
                'expected' => $storedOtp['code'],
                'received' => $request->otp
            ], 400);
        }

        if (now()->gt($storedOtp['expires_at'])) {
            Log::warning("Expired OTP for $originalPhone");
            return response()->json([
                'success' => false,
                'message' => 'OTP code has expired',
                'expired_at' => $storedOtp['expires_at']
            ], 400);
        }

        Cache::forget($key);
        Log::info("Successful verification for $originalPhone");

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully'
        ]);
    }
}