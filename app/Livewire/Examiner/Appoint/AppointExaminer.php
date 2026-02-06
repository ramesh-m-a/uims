<?php

namespace App\Livewire\Examiner\Appoint;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class AppointExaminer extends Component
{
    public ?int $yearId = null;
    public ?int $monthId = null;
    public ?int $schemeId = null;
    public ?int $degreeId = null;

    public function mount(): void
    {
        // Handle token → redirect to allocation
        if (request()->filled('token')) {
            try {
                [$yearId, $monthId, $schemeId, $degreeId] = $this->decryptToken(request()->get('token'));

              /*  $this->redirect(route('examiner.allocation', [
                    'yearId'   => $yearId,
                    'monthId'  => $monthId,
                    'schemeId' => $schemeId,
                    'degreeId' => $degreeId,
                ]));*/

                session([
                    'allocation.yearId'   => $yearId,
                    'allocation.monthId'  => $monthId,
                    'allocation.schemeId' => $schemeId,
                    'allocation.degreeId' => $degreeId,
                ]);

                if (auth()->user()->user_role_id === 3) {
                    // College → Go to college allocation screen
                    $this->redirect(route('examiner.college.allocation'));
                } else {
                    // Admin → Go to admin allocation screen
                    $this->redirect(route('examiner.allocation'));
                }

                return;
            } catch (\Throwable $e) {
                // Do NOT logout here during development
                abort(403, 'Invalid or expired token');
            }
        }
    }

    public function render()
    {
        return view('livewire.examiner.appoint.appoint-examiner', [
            'years' => DB::table('mas_year')
                ->where('mas_year_status_id', 1)
                ->orderBy('id')
                ->get(),

            'months' => $this->yearId
                ? DB::table('mas_month')
                    ->where('mas_month_status_id', 1)
                    ->orderBy('id')
                    ->get()
                : collect(),

            'schemes' => $this->monthId
                ? DB::table('mas_revised_scheme')
                    ->where('mas_revised_scheme_status_id', 1)
                    ->orderBy('id')
                    ->get()
                : collect(),
        ]);
    }

    public function updatedYearId()
    {
        $this->monthId = null;
        $this->schemeId = null;
        $this->degreeId = null;
    }

    public function updatedMonthId()
    {
        $this->schemeId = null;
        $this->degreeId = null;
    }

    public function updatedSchemeId()
    {
        $this->degreeId = null;
    }

    public function updatedDegreeId()
    {
        if (!$this->yearId || !$this->monthId || !$this->schemeId || !$this->degreeId) {
            return;
        }

        $token = $this->generateToken([
            'year_id'   => $this->yearId,
            'month_id'  => $this->monthId,
            'scheme_id' => $this->schemeId,
            'degree_id' => $this->degreeId,
        ]);

        $this->redirect("/examiner/appoint?token={$token}");
    }

    private function generateToken(array $data): string
    {
        $key = substr('rguhs_teacher_portal_2024', 0, 16);

        $json = json_encode($data, JSON_UNESCAPED_SLASHES);

        $cipherRaw = openssl_encrypt(
            $json,
            'aes-128-ecb',
            $key,
            OPENSSL_RAW_DATA
        );

        return bin2hex($cipherRaw);
    }

    private function decryptToken(string $hex): array
    {
        $cipherRaw = hex2bin($hex);
        if ($cipherRaw === false) {
            throw new \RuntimeException('Invalid hex');
        }

        $key = substr('rguhs_teacher_portal_2024', 0, 16);

        $json = openssl_decrypt(
            $cipherRaw,
            'aes-128-ecb',
            $key,
            OPENSSL_RAW_DATA
        );

        if (!$json) {
            throw new \RuntimeException('Decrypt failed');
        }

        $data = json_decode($json, true);

        return [
            (int)($data['year_id'] ?? 0),
            (int)($data['month_id'] ?? 0),
            (int)($data['scheme_id'] ?? 0),
            (int)($data['degree_id'] ?? 0),
        ];
    }

    public function resetFilters(): void
    {
        $this->yearId   = null;
        $this->monthId  = null;
        $this->schemeId = null;
        $this->degreeId = null;
    }
}
