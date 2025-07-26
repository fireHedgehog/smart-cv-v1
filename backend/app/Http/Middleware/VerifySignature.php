<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class VerifySignature
{
    public function handle($request, Closure $next)
    {
        $sig = $request->header('X-Signature');
        // Log::debug('🔑 [VerifySignature] X-Signature header', ['sig_base64' => $sig]);

        if (!$sig) {
            // Log::warning('❌ Missing signature header');
            return response()->json(['error' => 'Missing signature'], 401);
        }

        // ✅ 直接读 storage/keys/private.pem
        $privateKeyPath = storage_path('keys/private.pem');
        $rawKey = file_get_contents($privateKeyPath);

        if ($rawKey === false) {
            // Log::error('❌ Cannot read private key file', ['path' => $privateKeyPath]);
            return response()->json(['error' => 'Private key missing'], 500);
        }

        $privateKey = openssl_pkey_get_private($rawKey);
        if (!$privateKey) {
            // Log::error('❌ Invalid private key', ['openssl_error' => openssl_error_string()]);
            return response()->json(['error' => 'Invalid private key'], 500);
        }

        // 打印私钥 modulus SHA256
        $details = openssl_pkey_get_details($privateKey);
        $modulus_hash = hash('sha256', $details['rsa']['n']);
        // Log::debug('🔍 Private key modulus SHA256', ['hash' => $modulus_hash]);

        // 解码密文
        $encrypted = base64_decode($sig, true);
        /*
       Log::debug('📦 Base64 decoded signature', [
           'len' => strlen($encrypted),
           'first_bytes' => bin2hex(substr($encrypted, 0, 32)),
           'last_bytes' => bin2hex(substr($encrypted, -32))
       ]);
        */
        $decrypted = '';
        $ok = openssl_private_decrypt(
            $encrypted,
            $decrypted,
            $privateKey,
            OPENSSL_PKCS1_OAEP_PADDING
        );

        if (!$ok) {
            $err = '';
            while ($msg = openssl_error_string()) {
                $err .= $msg . " | ";
            }
            // Log::error('❌ Decrypt failed', ['openssl_error' => $err]);
            return response()->json(['error' => 'Invalid signature'], 401);
        }
        /*
               Log::debug('✅ Decrypted raw payload', [
                   'raw_hex' => bin2hex(substr($decrypted, 0, 64)),
                   'raw_string' => $decrypted
               ]);
         */
        $data = json_decode($decrypted, true);
        if (!$data) {
            // Log::warning('❌ JSON decode failed', ['raw' => $decrypted]);
            return response()->json(['error' => 'Invalid signature data'], 401);
        }

        $ts = $data['ts'] ?? 0;
        if (abs(time() - $ts) > 60) {
            // Log::warning('❌ Expired signature', ['ts' => $ts, 'now' => time()]);
            return response()->json(['error' => 'Invalid or expired signature'], 401);
        }

        /*
        Log::info('✅ Signature verified', [
            'ts' => $ts,
            'payload' => $data['payload'] ?? null
        ]);
        */

        return $next($request);
    }
}
