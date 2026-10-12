<?php

namespace App\Services;

use App\Mail\TestimonialPendingAdminMail;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

        // Kirim email notifikasi ke admin jika email terkonfigurasi
        $adminEmail = SiteSetting::get('company.email', config('mail.from.address'));
        if (filled($adminEmail)) {
            Mail::to($adminEmail)->queue(new TestimonialPendingAdminMail($testimonial));
        }

        Log::info("Testimoni baru dari {$testimonial->name} menunggu persetujuan.");

        return $testimonial;
    }
}
