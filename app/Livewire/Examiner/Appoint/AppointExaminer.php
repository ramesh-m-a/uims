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

    /* =========================================
       🔐 MOUNT — TOKEN ENTRY ONLY
    ========================================= */
    public function mount(): void
    {
        if (!request()->filled('token')) {
            return;
        }

        if (request()->filled('m')) {
            session([
                'appoint.module' => request('m') === 'a'
                    ? 'appointment'
                    : 'allocation'
            ]);
        }

        try {

            $data = $this->decryptToken(request()->get('token'));

            /**
             * ⭐ EXPIRY CHECK
             */
            if (!empty($data['exp']) && now()->timestamp > $data['exp']) {
                abort(403, 'Token expired');
            }

            /**
             * ⭐ SESSION CONTEXT
             */
            session([
                'allocation.yearId'   => (int)$data['year_id'],
                'allocation.monthId'  => (int)$data['month_id'],
                'allocation.schemeId' => (int)$data['scheme_id'],
                'allocation.degreeId' => (int)$data['degree_id'],
            ]);

            /**
             * ⭐ MODULE SWITCH (TOKEN ONLY)
             */
            $module = $data['module'] ?? 'allocation';

            if ($module === 'appointment') {
                $this->redirect(route('examiner.appointment-order.view'));
                return;
            }

            /**
             * ⭐ DEFAULT FLOW (NO BREAKING CHANGE)
             */
            if (auth()->user()->user_role_id === 3) {
                $this->redirect(route('examiner.college.allocation'));
            } else {
                $this->redirect(route('examiner.allocation'));
            }

        } catch (\Throwable $e) {

            abort(403, 'Invalid or expired token');

        }
    }

    /* =========================================
       RENDER
    ========================================= */
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

    /* =========================================
       DROPDOWN FLOW
    ========================================= */
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

        /**
         * ⭐ DEFAULT MODULE (ALLOCATION)
         * Menu entry can override later
         */
        $module = session('appoint.module', 'allocation');

        $token = $this->generateToken([
            'year_id'   => $this->yearId,
            'month_id'  => $this->monthId,
            'scheme_id' => $this->schemeId,
            'degree_id' => $this->degreeId,
            'module'    => $module,
            'exp'       => now()->addMinutes(30)->timestamp,
        ]);

        $this->redirect("/examiner/appoint?token={$token}");
    }

    /* =========================================
       🔐 TOKEN GENERATION
    ========================================= */
    private function generateToken(array $data): string
    {
        $key = substr(config('app.key'), 0, 16);

        $json = json_encode($data, JSON_UNESCAPED_SLASHES);

        $cipherRaw = openssl_encrypt(
            $json,
            'aes-128-ecb',
            $key,
            OPENSSL_RAW_DATA
        );

        return bin2hex($cipherRaw);
    }

    /* =========================================
       🔐 TOKEN DECRYPT
    ========================================= */
    private function decryptToken(string $hex): array
    {
        $cipherRaw = hex2bin($hex);

        if ($cipherRaw === false) {
            throw new \RuntimeException('Invalid token');
        }

        $key = substr(config('app.key'), 0, 16);

        $json = openssl_decrypt(
            $cipherRaw,
            'aes-128-ecb',
            $key,
            OPENSSL_RAW_DATA
        );

        if (!$json) {
            throw new \RuntimeException('Decrypt failed');
        }

        return json_decode($json, true) ?? [];
    }

    public function resetFilters(): void
    {
        $this->yearId   = null;
        $this->monthId  = null;
        $this->schemeId = null;
        $this->degreeId = null;
    }
}
