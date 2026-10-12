<?php

namespace App\Services;

use App\Models\Testimonial;
use Illuminate\Support\Facades\Log;

class TestimonialSubmissionService
{
    public function submit(array $data, ?array $files = null): Testimonial
    {
        $photos = [];

        if ($files) {
            foreach ($files as $file) {
                // Resize handling could be added here if Intervention Image is available
                // For now we just store
                $photos[] = $file->store('testimonials', 'public');
            }
        }

        $testimonial = Testimonial::create([
            'name' => strip_tags($data['name']),
            'role_or_info' => strip_tags($data['role_or_info'] ?? 'Pasien SAM'),
            'content' => strip_tags($data['content']),
            'rating' => $data['rating'],
            'photos' => ! empty($photos) ? $photos : null,
            'status' => 'pending',
            'source' => 'public',
            'is_active' => false,
            'consent_confirmed' => $data['consent_confirmed'] ?? false,
        ]);

        // Send email notification (dummy/log for now as email to admin needs to be implemented properly, or we can just log if no Mailable exists yet)
        Log::info("Testimoni baru dari {$testimonial->name} menunggu persetujuan.");

        return $testimonial;
    }
}
