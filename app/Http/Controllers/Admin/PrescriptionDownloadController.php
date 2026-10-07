<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Unduh resep dokter dari disk privat. Hanya untuk admin yang login.
 */
class PrescriptionDownloadController extends Controller
{
    public function __invoke(Appointment $appointment): StreamedResponse
    {
        $path = $appointment->prescription_path;

        abort_if(blank($path) || ! Storage::disk('local')->exists($path), 404);

        $extension = pathinfo($path, PATHINFO_EXTENSION);

        return Storage::disk('local')->download(
            $path,
            "resep-{$appointment->code}.{$extension}",
            ['Cache-Control' => 'no-store, private'],
        );
    }
}
