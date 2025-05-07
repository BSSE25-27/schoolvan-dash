<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Operator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use AfricasTalking\SDK\AfricasTalking;
use Exception;
use App\Utilities\PhoneNumberUtility;
use Twilio\Rest\Client;


/**
 * @group Operator Auth management
 *
 * APIs for managing van operator authentication
 */
class OperatorController extends Controller
{
    //
    public function operatorLogin(Request $request) {
        $validated = $request->validate([
            'VanOperatorName' => 'required|string|max:255',
            'PhoneNumber' => 'required|string|max:20',// Make sure this matches your frontend field name
        ]);
    
        $operator = Operator::where('VanOperatorName', $validated['VanOperatorName'])
            ->where('PhoneNumber', $validated['PhoneNumber']) // Using the validated data
            ->first();
    
        if (!$operator) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    
        return response()->json([
            'message' => 'Login successful',
            'operator' => $operator,
        ]);
    }   
    public function resendOtp(Request $request) {
        $request->validate([
            'phone_number' => 'required|string',
        ]);
    
        $formattedPhone = PhoneNumberUtility::formatForSms($request->phone_number);
    
        $otp = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(5);
    
        Cache::put('otp_' . $request->phone_number, [
            'code' => $otp,
            'expires_at' => $expiresAt
        ], $expiresAt);
    
        $accountSid = env('TWILIO_ACCOUNT_SID');
        $authToken = env('TWILIO_AUTH_TOKEN');
        $twilioNumber = env('TWILIO_PHONE_NUMBER');
    
        try {
            $client = new Client($accountSid, $authToken);
            
            $message = $client->messages->create(
                $formattedPhone,
                [
                    'from' => $twilioNumber,
                    'body' => "Your OTP code is: $otp. Valid for 5 minutes."
                ]
            );
    
            return response()->json([
                'success' => true,
                'message' => 'OTP resent successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP: ' . $e->getMessage()
            ], 500);
        }
    }
    
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
    
    public function sendOtp(Request $request) {
        $request->validate(['phone_number' => 'required|string']);
        
        // Same implementation as your resend-otp
        return $this->resendOtp($request);
    }
}
