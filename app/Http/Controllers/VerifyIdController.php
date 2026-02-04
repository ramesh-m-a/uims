<?php

namespace App\Http\Controllers;

use App\Models\Admin\User;
use App\Models\IdScanLog;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class VerifyIdController extends Controller
{
    public function __invoke(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return $this->invalid('Missing verification token');
        }

        try {
            $decoded = JWT::decode($token, new Key(config('app.key'), 'HS256'));
        } catch (\Throwable $e) {

            IdScanLog::create([
                'user_id' => 0,
                'ip' => $request->ip(),
                'device_hash' => sha1($request->userAgent() ?? ''),
                'user_agent' => $request->userAgent(),
                'valid' => false,
                'scanned_at' => now(),
            ]);

            return $this->invalid('Invalid or expired QR code');
        }

        $currentDevice = sha1($request->userAgent() ?? '');
        $valid = $decoded->device === $currentDevice;

        $user = User::find($decoded->sub);

        IdScanLog::create([
            'user_id' => $user?->id ?? 0,
            'ip' => $request->ip(),
            'device_hash' => $currentDevice,
            'user_agent' => $request->userAgent(),
            'valid' => $valid,
            'scanned_at' => now(),
        ]);

        if (!$valid) {
            return $this->invalid('This QR was copied from another device');
        }

        if (!$user) {
            return $this->invalid('User not found');
        }

        return view('verify.id', [
            'user' => $user,
            'payload' => $decoded
        ]);
    }

    protected function invalid(string $reason)
    {
        return response()->view('verify.invalid', [
            'reason' => $reason
        ], 403);
    }
}
