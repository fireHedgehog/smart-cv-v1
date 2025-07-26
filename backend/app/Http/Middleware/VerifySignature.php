<?php

namespace App\Http\Middleware;

use Closure;

class VerifySignature
{
    public function handle($request, Closure $next)
    {
        $sig = $request->header('X-Signature');
        if (!$sig) {
            return response()->json(['error' => 'Missing signature'], 401);
        }

        $privateKey = openssl_pkey_get_private(str_replace('\n', "\n", env('API_PRIVATE_KEY')));

        $encrypted = base64_decode($sig);
        $decrypted = '';
        openssl_private_decrypt($encrypted, $decrypted, $privateKey);

        $data = json_decode($decrypted, true);
        if (!$data || abs(time() - $data['ts']) > 60) {
            return response()->json(['error' => 'Invalid or expired signature'], 401);
        }

        return $next($request);
    }
}
