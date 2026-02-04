<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class IdCardPdfControllerdelete extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();

        // =========================
        // Resolve photo safely (spaces, slashes, any extension)
        // =========================
        $raw = $user->photo_path ?? null;
        $realPath = null;

        if ($raw) {
            $path = trim($raw);
            $path = preg_replace('#/+#', '/', $path);
            $path = ltrim($path, '/');
            $path = strtok($path, '?');
            $path = urldecode($path);

            $baseDir = dirname($path);
            $filename = pathinfo($path, PATHINFO_FILENAME);

            $diskDir = public_path('storage/' . $baseDir);

            if (is_dir($diskDir)) {
                foreach (glob($diskDir . '/*') as $file) {
                    if (stripos(pathinfo($file, PATHINFO_FILENAME), $filename) !== false) {
                        if (file_exists($file)) {
                            $realPath = $file;
                            break;
                        }
                    }
                }
            }
        }

        if (!$realPath || !file_exists($realPath)) {
            $realPath = public_path('images/RGUHS-logo-AA.png');
        }

        // =========================
        // QR using chillerlan (already installed)
        // =========================
        $payload = base64_encode($user->id . '|' . now()->timestamp);

        $options = new QROptions([
            'outputType'  => QRCode::OUTPUT_IMAGE_PNG,
            'scale'       => 6,
            'imageBase64' => true,
        ]);

        $qrBase64 = (new QRCode($options))->render(
            url('/verify/' . $payload)
        );

        // =========================
        // Build PDF
        // =========================
        $pdf = Pdf::loadView('pdf.id-card', [
            'cardGenerated' => true,

            // Text fields
            'university'  => 'Rajiv Gandhi University of Health Sciences, Karnatakaaaaaa',
            'college'     => $user->college?->MAS_COLLEGE_NAME ?? '',
            'stream'      => $user->stream?->MAS_STREAM_NAME ?? '',
            'name'        => $user->name,
            'designation' => $user->designation?->mas_designation_name ?? '',
            'department'  => $user->department?->mas_department_name ?? '',
            'tin'         => $user->tin,



            // Image fields (match blade)
            'logoBase64'  => 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/RGUHS-logo-AA.png'))),
            'photoBase64' => 'data:' . mime_content_type($realPath) . ';base64,' . base64_encode(file_get_contents($realPath)),
            'qrBase64'    => $qrBase64,
        ])->setPaper('A4', 'portrait');

        return $pdf->download('id-card.pdf');
    }
}
