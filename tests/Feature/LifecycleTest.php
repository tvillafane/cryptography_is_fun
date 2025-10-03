<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PublicKey;
use App\Models\Nonce;
use App\Services\HolderService;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LifecycleTest extends TestCase
{
    use RefreshDatabase;

    private $holder;
    private $keys;

    protected function setUp(): void
    {
        parent::setUp();

        $this->holder = new HolderService();
        $this->keys   = $this->holder->generateKeyPair();
    }

    public function testFullHappyPathWithReplayBounce()
    {
        $keys = $this->holder->generateKeyPair();

        $register_response = $this->postJson('/api/register', [
            'public_key' => $keys['public_key']
        ]);

        $register_response->assertStatus(201);

        $public_key_id = $register_response->json()['public_key_id'];

        $nonce_response = $this->getJson('/api/nonce?key_id=' . $public_key_id);
        $nonce_response->assertStatus(201);
        $nonce = $nonce_response->json('nonce');

        $signed_payload = $this->holder->generateSignedPayload(
            $keys['private_key'],
            $nonce,
            'hello SpruceId',
            $public_key_id
        );

        $verify_response = $this->postJson('/api/verify', $signed_payload);
        $verify_response->assertStatus(200);

        $used_nonce_count = PublicKey::find($public_key_id)->nonces()->whereNotNull('used_at')->count();

        $this->assertEquals(1, $used_nonce_count);

        //  try again and get bounced
        $second_attempt_response = $this->postJson('/api/verify', $signed_payload);
        $second_attempt_response->assertStatus(400);
    }
}
