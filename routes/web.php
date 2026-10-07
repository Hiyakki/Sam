<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Api\AppointmentApiController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ManageAppointmentController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Public Pages (Tanpa Login)
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/katalog/{slug}', [CatalogController::class, 'show'])->name('catalog.show');
Route::get('/faq', [FaqController::class, 'index'])->name('faq');
Route::get('/kebijakan-privasi', [PageController::class, 'privacy'])->name('privacy');

/*
|--------------------------------------------------------------------------
| Appointment Flow Routes
|--------------------------------------------------------------------------
*/
Route::get('/appointment', [AppointmentController::class, 'index'])->name('appointment');
Route::post('/appointment', [AppointmentController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('appointment.store');

Route::get('/janji/konfirmasi/{code}', [AppointmentController::class, 'confirmation'])
    ->name('appointment.confirmation');

Route::get('/janji/{code}', [ManageAppointmentController::class, 'show'])
    ->middleware('throttle:30,1')
    ->name('appointment.manage');

Route::post('/janji/{code}/reschedule', [ManageAppointmentController::class, 'reschedule'])
    ->middleware('throttle:10,1')
    ->name('appointment.reschedule');

Route::post('/janji/{code}/batal', [ManageAppointmentController::class, 'cancel'])
    ->middleware('throttle:10,1')
    ->name('appointment.cancel');

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
