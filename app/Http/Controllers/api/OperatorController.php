<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Operator;
use Illuminate\Support\Facades\Cache;
use AfricasTalking\SDK\AfricasTalking;
use Exception;


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

        public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'otp' => 'required|digits:5',
        ]);

        $key = 'otp_' . $request->phone_number;
        $storedOtp = Cache::get($key);

        if (!$storedOtp || $storedOtp['code'] != $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP code'
            ], 400);
        }

        if (now()->gt($storedOtp['expires_at'])) {
            return response()->json([
                'success' => false,
                'message' => 'OTP code has expired'
            ], 400);
        }

        Cache::forget($key);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully'
        ]);
    }

        public function resendOtp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
        ]);
    
        // Generate 5-digit OTP
        $otp = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(5);
    
        // Store OTP in cache
        Cache::put('otp_' . $request->phone_number, [
            'code' => $otp,
            'expires_at' => $expiresAt
        ], $expiresAt);
    
        // Initialize Africa's Talking
        $username = env('AT_USERNAME'); // Your Africa's Talking username
        $apiKey = env('AT_KEY');    // Your Africa's Talking API key
        
        $AT = new AfricasTalking($username, $apiKey);
        $sms = $AT->sms();
    
        try {
            // Send SMS via Africa's Talking
            $result = $sms->send([
                'to'      => $request->phone_number,
                'message' => "Your OTP code is: $otp. Valid for 5 minutes.",
                // 'from'    => env('AFRICASTALKING_SENDER_ID', 'YOUR_SENDER_ID')
            ]);
    
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
}
