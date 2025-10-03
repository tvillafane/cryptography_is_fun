<?php

namespace App\Http\Controllers;

use App\Models\Nonce;
use App\Models\PublicKey;
use Illuminate\Http\Request;

class KeysController extends Controller
{
    public function registerPublicKey(Request $request)
    {
        $keystring = $request->input('public_key');

        if (!openssl_pkey_get_public($keystring)) {
            return response()->json([
                'error' => 'invalid public key format'
            ], 422);
        }

        $key = PublicKey::create([
            'body' => $keystring
        ]);

        return response()->json([
            'public_key_id' => $key->id
        ], 201);
    }

    public function generateNonceForKey(Request $request)
    {
        $key_id = $request->input('key_id');

        $key = PublicKey::find($key_id);

        if (!$key) {
            return response()->json([
                'error' => 'this key does not exist'
            ], 404);
        }

        $body = base64_encode(random_bytes(32));

        $nonce = $key->nonces()->create([
            'body'       => $body,
            'expires_at' => now()->addMinutes(15)
        ]);

        return response()->json([
            'nonce'      => $body,
            'expires_at' => $nonce->expires_at
        ], 201);
    }

    public function verifyMessage(Request $request)
    {
        $request->validate([
            'payload'   => 'required|string',
            'nonce'     => 'required|string',
            'signature' => 'required|string',
            'key_id'    => 'required|integer',
        ]);

        $public_key = PublicKey::find($request->input('key_id'));

        if (!$public_key) {
            return response()->json(['error' => 'Public key not found'], 404);
        }

        $nonce = Nonce::where('body', $request->input('nonce'))
            ->where('public_key_id', $public_key->id)
            ->first();

        if (!$nonce) {
            return response()->json(['error' => 'Nonce not found'], 404);
        }

        if ($nonce->used_at) {
            return response()->json(['error' => 'Nonce already used'], 412);  //  precondition failed
        }

        if ($nonce->expires_at < now()) {
            return response()->json(['error' => 'Nonce expired'], 400);
        }

        $message             = $request->input('payload') . $nonce->body;
        $signature           = base64_decode($request->input('signature'));
        $key_resource        = openssl_pkey_get_public($public_key->body);
        $verification_status = openssl_verify($message, $signature, $key_resource, OPENSSL_ALGO_SHA256);

        if ($verification_status === 1) {
            $nonce->update([
                'used_at' => now()
            ]);
          
            return response()->json(['status' => 'verified'], 200);
        } elseif ($verification_status === 0) {
            return response()->json(['error' => 'Invalid signature'], 400);
        } else {
            return response()->json(['error' => 'Verification error'], 500);
        }
    }
}
