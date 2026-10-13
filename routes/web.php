<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Api\AppointmentApiController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TestimonialController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Public Pages (Tanpa Login)
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/cabang/{slug}', [BranchController::class, 'show'])->name('branch.show');
Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/katalog/{slug}', [CatalogController::class, 'show'])->name('catalog.show');
Route::get('/faq', [FaqController::class, 'index'])->name('faq');
Route::get('/kebijakan-privasi', [PageController::class, 'privacy'])->name('privacy');

Route::get('/testimoni', [TestimonialController::class, 'index'])->name('testimonials.index');
Route::post('/testimoni', [TestimonialController::class, 'store'])
    ->middleware('throttle:3,60')
    ->name('testimonials.store');

/*
|--------------------------------------------------------------------------
| Appointment Flow Routes
|--------------------------------------------------------------------------
*/
Route::get('/appointment', [AppointmentController::class, 'index'])->name('appointment');
Route::post('/appointment', [AppointmentController::class, 'store'])
    ->middleware('throttle:appointment.submission')
    ->name('appointment.store');

Route::get('/janji/konfirmasi/{code}', [AppointmentController::class, 'confirmation'])
    ->middleware('throttle:appointment.confirmation')
    ->name('appointment.confirmation');

/*
|--------------------------------------------------------------------------
| Interactive Appointment API Routes (JSON)
|--------------------------------------------------------------------------
*/
Route::prefix('api/appointment')->middleware('throttle:60,1')->group(function () {
    Route::get('/branches/{branch}/services', [AppointmentApiController::class, 'services'])
        ->name('api.appointment.services');
    Route::get('/closed-dates', [AppointmentApiController::class, 'closedDates'])
        ->name('api.appointment.closed-dates');
    Route::get('/slots', [AppointmentApiController::class, 'slots'])
        ->name('api.appointment.slots');
});

/*
|--------------------------------------------------------------------------
| SEO Routes (Sitemap & Robots)
|--------------------------------------------------------------------------
*/
Route::get('/sitemap.xml', [SitemapController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');
