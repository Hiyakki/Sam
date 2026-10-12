<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTestimonialRequest;
use App\Models\Testimonial;
use App\Services\TestimonialSubmissionService;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::with('product')->active()->paginate(15);

        return view('pages.testimonials', compact('testimonials'));
    }

    public function store(StoreTestimonialRequest $request, TestimonialSubmissionService $service)
    {
        $service->submit($request->validated(), $request->file('photos'));

        return back()->with('success', 'Terima kasih! Ulasan Anda akan tampil setelah kami tinjau.');
    }
}
