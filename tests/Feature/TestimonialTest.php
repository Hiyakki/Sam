<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TestimonialTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_view_approved_testimonials()
    {
        Testimonial::factory()->create([
            'status' => 'approved',
            'is_active' => true,
            'consent_confirmed' => true,
            'name' => 'John Doe',
        ]);

        Testimonial::factory()->create([
            'status' => 'pending',
            'is_active' => false,
            'consent_confirmed' => true,
            'name' => 'Jane Doe',
        ]);

        $response = $this->get('/testimoni');

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertDontSee('Jane Doe');
    }

    public function test_public_can_submit_testimonial_and_saved_as_pending()
    {
        Storage::fake('public');

        $response = $this->post('/testimoni', [
            'name' => 'Alice',
            'rating' => 5,
            'content' => 'Pelayanan yang sangat memuaskan, luar biasa!',
            'consent_confirmed' => 1,
            'website_url' => '', // Honeypot
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('testimonials', [
            'name' => 'Alice',
            'status' => 'pending',
            'is_active' => false,
        ]);
    }

    public function test_submission_without_consent_is_rejected()
    {
        $response = $this->post('/testimoni', [
            'name' => 'Alice',
            'rating' => 5,
            'content' => 'Pelayanan yang sangat memuaskan, luar biasa!',
            'website_url' => '',
        ]);

        $response->assertSessionHasErrors('consent_confirmed');
    }

    public function test_rate_limit_applies_to_submissions()
    {
        for ($i = 0; $i < 3; $i++) {
            $this->post('/testimoni', [
                'name' => 'User '.$i,
                'rating' => 5,
                'content' => 'Pelayanan sangat bagus dan luar biasa '.$i,
                'consent_confirmed' => 1,
            ]);
        }

        $response = $this->post('/testimoni', [
            'name' => 'User 4',
            'rating' => 5,
            'content' => 'Pelayanan sangat bagus dan luar biasa 4',
            'consent_confirmed' => 1,
        ]);

        $response->assertStatus(429); // Too Many Requests
    }

    public function test_photo_validation_works()
    {
        Storage::fake('public');

        // Valid submission
        $file = UploadedFile::fake()->image('photo.jpg')->size(1024);

        $response = $this->post('/testimoni', [
            'name' => 'Alice',
            'rating' => 5,
            'content' => 'Pelayanan yang sangat memuaskan, luar biasa!',
            'consent_confirmed' => 1,
            'photos' => [$file],
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseCount('testimonials', 1);

        // Invalid submission (not an image)
        $invalidFile = UploadedFile::fake()->create('document.pdf', 1024);

        $response2 = $this->post('/testimoni', [
            'name' => 'Bob',
            'rating' => 5,
            'content' => 'Pelayanan yang sangat memuaskan, luar biasa!',
            'consent_confirmed' => 1,
            'photos' => [$invalidFile],
        ]);

        $response2->assertSessionHasErrors('photos.0');
    }
}
