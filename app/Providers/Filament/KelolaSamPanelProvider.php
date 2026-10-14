<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Widgets\AppointmentsTableWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Http\Controllers\Admin\PrescriptionDownloadController;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Vite;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class KelolaSamPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('kelola-sam')
            ->path('kelola-sam')
            ->login(Login::class)
            // Registrasi dan Lupa Password DINONAKTIFKAN
            ->brandName('SAM Admin')
            ->brandLogo(fn () => asset('images/logo-sam.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('images/logo-sam.png'))
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn () => app(Vite::class)('resources/css/filament/kelola-sam/theme.css')
            )
            ->colors([
                // Skala Navy turunan #1E2A4A (shade 50-950 ramah mode terang dan gelap)
                'primary' => [
                    50 => '240, 244, 250',
                    100 => '224, 233, 245',
                    200 => '195, 212, 235',
                    300 => '155, 185, 220',
                    400 => '105, 150, 198',
                    500 => '65, 110, 165',
                    600 => '30, 42, 74',     // #1E2A4A (core primary navy)
                    700 => '24, 34, 60',     // #18223C
                    800 => '20, 29, 54',     // #141D36 (primary-dark)
                    900 => '15, 21, 40',     // #0F1528
                    950 => '10, 14, 27',     // #0A0E1B
                ],
                'danger' => Color::hex('#E8343B'),
                'success' => Color::Emerald,
                'warning' => Color::Amber,
                'info' => Color::Sky,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                StatsOverviewWidget::class,
                AppointmentsTableWidget::class,
            ])
            ->navigationGroups([
                'Appointment',
                'Klinik & Cabang',
                'Katalog',
                'Website & Konten',
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            // File privat (resep dokter) hanya bisa diunduh admin yang login.
            ->authenticatedRoutes(function () {
                Route::get('resep/{appointment}', PrescriptionDownloadController::class)
                    ->name('prescription.download');
            });
    }
}
