<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\IdCardService;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Firebase\JWT\JWT;
use chillerlan\QRCode\Common\EccLevel;

class IdCard extends Component
{
    public bool $cardGenerated = false;

    public string $university = 'Rajiv Gandhi University of Health Sciences, Karnataka';
    public string $college = '';
    public string $stream = '';
    public string $name = '';
    public string $designation = '';
    public string $department = '';
    public string $tin = '';
    public string $teacherId = '';

    public string $serial = '';
    public string $issueDate = '';
    public string $expiryDate = '';

    public string $logoBase64 = '';
    public string $photoBase64 = '';
    public string $qrBase64 = '';

    public function mount()
    {
        if (Auth::id() === 1 || request()->boolean('preview')) {
            $this->demo();
        } else {
            $this->load();
        }

        if ($this->cardGenerated) {
            $this->buildAssets();
        }
    }

    public function render()
    {
        return view('livewire.profile.id-card');
    }

    protected function demo()
    {
        $this->cardGenerated = true;
        $this->name = 'ARUN B J';
        $this->designation = 'Assistant Professor';
        $this->department = 'Respiratory Medicine';
        $this->college = 'Bangalore Medical College & Research Institute';
        $this->stream = 'Medical';
        $this->teacherId = '27715';
        $this->tin = '13424312';

        $this->buildMeta();
    }

    protected function load()
    {
        $data = app(IdCardService::class)->getByUserId(auth()->id());
        if (!$data) return;

        $this->cardGenerated = true;

        $this->name = $data['name'];
        $this->designation = $data['designation'];
        $this->department = $data['department'];
        $this->college = $data['college'];
        $this->stream = $data['stream'];
        $this->teacherId = $data['teacher_id'];
        $this->tin = $data['tin'];

        $this->buildMeta();
    }

    protected function buildMeta()
    {
        $this->serial = 'RGUHS-' . now()->year . '-' . str_pad($this->teacherId, 6, '0', STR_PAD_LEFT);
        $this->issueDate = now()->format('d M Y');
        $this->expiryDate = now()->addYears(3)->format('d M Y');
    }

    protected function buildAssets(): void
    {
        // =========================
        // LOGO (Base64)
        // =========================
        $logoPath = public_path('images/RGUHS-logo-AA.png');

        $this->logoBase64 = 'data:image/png;base64,' . base64_encode(
                file_get_contents($logoPath)
            );

        // =========================
        // STABLE PAYLOAD
        // =========================
        $payload  = sha1('RGUHS|' . $this->teacherId);
        $verifyUrl = url('/verify/' . $payload);

        // =========================
        // QR OPTIONS (PROPER ECC)
        // =========================
        $options = new QROptions([
            'outputType'      => QRCode::OUTPUT_IMAGE_PNG,
            'scale'           => 8,
            'imageBase64'     => false,
            'eccLevel'        => EccLevel::H,   // 🔥 REQUIRED FOR LOGO
            'addLogoSpace'    => true,
            'logoSpaceWidth'  => 22,
            'logoSpaceHeight' => 22,
        ]);

        // =========================
        // GENERATE QR (binary PNG)
        // =========================
        $qrBinary = (new QRCode($options))->render($verifyUrl);

        // =========================
        // EMBED LOGO USING GD
        // =========================
        $qrImage   = imagecreatefromstring($qrBinary);
        $logoImage = imagecreatefrompng($logoPath);

        $qrWidth   = imagesx($qrImage);
        $qrHeight  = imagesy($qrImage);

        $logoSize = (int) ($qrWidth * 0.25); // 25% center
        $logoX = ($qrWidth - $logoSize) / 2;
        $logoY = ($qrHeight - $logoSize) / 2;

        imagecopyresampled(
            $qrImage,
            $logoImage,
            $logoX,
            $logoY,
            0,
            0,
            $logoSize,
            $logoSize,
            imagesx($logoImage),
            imagesy($logoImage)
        );

        // =========================
        // CAPTURE FINAL PNG
        // =========================
        ob_start();
        imagepng($qrImage);
        $finalQrBinary = ob_get_clean();

        imagedestroy($qrImage);
        imagedestroy($logoImage);

        // =========================
        // STORE BASE64
        // =========================
        $this->qrBase64 = 'data:image/png;base64,' . base64_encode($finalQrBinary);

        // =========================
// PHOTO (Base64)  🔥 ADD THIS
// =========================
        $raw = Auth::user()->photo_path ?? null;
        $realPath = null;

        if ($raw) {
            $path = trim($raw);
            $path = preg_replace('#/+#', '/', $path);
            $path = ltrim($path, '/');
            $path = strtok($path, '?');
            $path = urldecode($path);

            $baseDir  = dirname($path);
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

// fallback
        if (!$realPath || !file_exists($realPath)) {
            $realPath = public_path('images/default-avatar.png');
        }

        $mime = mime_content_type($realPath);
        $this->photoBase64 = "data:$mime;base64," . base64_encode(file_get_contents($realPath));

    }

    public function downloadPdf()
    {
        $pdf = Pdf::loadView('pdf.id-card', [
            'card' => $this
        ])->setPaper('A4', 'portrait');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'id-card.pdf'
        );
    }
}
