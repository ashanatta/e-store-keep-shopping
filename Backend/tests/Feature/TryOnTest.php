<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TryOnTest extends TestCase
{
    use RefreshDatabase;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Storage::disk('public')->put('products/dress.jpg', UploadedFile::fake()->image('dress.jpg')->getContent());

        $this->product = Product::forceCreate(['name' => 'Summer Dress', 'image' => 'products/dress.jpg']);

        config([
            'services.tryon.url' => 'http://tryon.test',
            'services.tryon.secret' => 'shh',
        ]);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'product_id' => $this->product->id,
            'garment_image' => 'products/dress.jpg',
            'person_image' => UploadedFile::fake()->image('me.jpg', 600, 900),
        ], $overrides);
    }

    public function test_guests_cannot_start_a_try_on(): void
    {
        $this->postJson('/api/try-on', $this->payload())->assertUnauthorized();
    }

    public function test_returns_503_when_service_is_not_running(): void
    {
        Http::fake(fn () => throw new ConnectionException('Connection refused'));

        $this->actingAs(User::factory()->create())
            ->postJson('/api/try-on', $this->payload())
            ->assertStatus(503);
    }

    public function test_rejects_images_that_do_not_belong_to_the_product(): void
    {
        Http::fake();

        $this->actingAs(User::factory()->create())
            ->postJson('/api/try-on', $this->payload(['garment_image' => 'https://evil.example/x.jpg']))
            ->assertStatus(422)
            ->assertJsonValidationErrors('garment_image');

        Http::assertNothingSent();
    }

    public function test_full_try_on_flow(): void
    {
        Http::fake([
            'tryon.test/run' => Http::response(['id' => 'job-123'], 202),
            'tryon.test/status/job-123' => Http::sequence()
                ->push(['status' => 'processing'])
                ->push(['status' => 'completed', 'image' => 'data:image/jpeg;base64,AAAA']),
        ]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/try-on', $this->payload())
            ->assertStatus(202)
            ->assertJson(['id' => 'job-123']);

        Http::assertSent(function ($request) {
            return $request->url() === 'http://tryon.test/run'
                && $request->hasHeader('X-Tryon-Secret', 'shh')
                && $request['description'] === 'Summer Dress'
                && str_starts_with($request['person_image'], 'data:image/')
                && str_starts_with($request['garment_image'], 'data:image/');
        });

        // Another user can't poll someone else's job.
        $this->actingAs(User::factory()->create())
            ->getJson('/api/try-on/job-123')
            ->assertNotFound();

        $this->actingAs($user)
            ->getJson('/api/try-on/job-123')
            ->assertOk()
            ->assertJson(['status' => 'processing']);

        $this->actingAs($user)
            ->getJson('/api/try-on/job-123')
            ->assertOk()
            ->assertJson(['status' => 'completed', 'image' => 'data:image/jpeg;base64,AAAA']);
    }

    public function test_failed_job_returns_friendly_message(): void
    {
        Http::fake([
            'tryon.test/run' => Http::response(['id' => 'job-456'], 202),
            'tryon.test/status/job-456' => Http::response([
                'status' => 'failed',
                'error' => ['name' => 'QuotaError', 'message' => 'Free GPU quota reached.'],
            ]),
        ]);

        $user = User::factory()->create();
        $this->actingAs($user)->postJson('/api/try-on', $this->payload())->assertStatus(202);

        $this->actingAs($user)
            ->getJson('/api/try-on/job-456')
            ->assertOk()
            ->assertJson(['status' => 'failed'])
            ->assertJsonPath('message', fn ($message) => str_contains($message, 'free AI limit'));
    }

    public function test_service_error_returns_502(): void
    {
        Http::fake(['tryon.test/run' => Http::response(['detail' => 'boom'], 500)]);

        $this->actingAs(User::factory()->create())
            ->postJson('/api/try-on', $this->payload())
            ->assertStatus(502);
    }
}
