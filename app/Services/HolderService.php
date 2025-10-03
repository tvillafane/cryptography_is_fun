<?php 

namespace App\Services;

class HolderService {
    public function generateKeyPair()
    {
        $config = [
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ];

        $keypair = openssl_pkey_new($config);

        openssl_pkey_export($keypair, $private_key_pem);

        $key_details    = openssl_pkey_get_details($keypair);
        $public_key_pem = $key_details["key"];

        return [
            'public_key'  => $public_key_pem,
            'private_key' => $private_key_pem
        ];
    }

    public function generateSignedPayload($private_key, $nonce, $payload, $public_key_id)
    {
        $message = $payload . $nonce;

        openssl_sign($message, $signature, $private_key, OPENSSL_ALGO_SHA256);

        $base_64_signature = base64_encode($signature);

        return [
            'payload'   => $payload,
            'nonce'     => $nonce,
            'signature' => $base_64_signature,
            'key_id'    => $public_key_id,
        ];
    }
}