<?php

namespace App\Livewire\Profile\Tabs;

use App\Models\UserTinAudit;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\UserProfileDraft;
use Illuminate\Support\Facades\DB;

class BankTab extends Component
{
    public UserProfileDraft $draft;

    /* ================= MASTERS ================= */
    public $salaryModes = [];
    public $accountTypes = [];

    /* ================= STATE ================= */
    public array $bank = [
        'identity' => [
            'pan_number'     => null,
            'pan_name'       => null,
            'aadhar_number'  => null,
        ],
        'salary' => [
            'basic_pay'      => null,
            'salary_mode_id' => null,
        ],
        'account' => [
            'account_type_id' => null,
            'account_number'  => null,
            'account_name'    => null,
            'ifs_code'       => null,
            'bank_name'       => null,
            'branch_name'     => null,
        ],
    ];

    /* ================= MOUNT ================= */

    public function mount(UserProfileDraft $draft): void
    {
        $this->draft = $draft;

      //  dd('mounting bank',$draft );
        // -------------------------------
        // LOAD SALARY MODE MASTER
        // -------------------------------
        $this->salaryModes = DB::table('mas_salary_mode')
            ->where('mas_salary_mode_status_id', 1)
            ->orderBy('mas_salary_mode_name')
            ->get();

        // -------------------------------
        // LOAD ACCOUNT TYPE MASTER
        // -------------------------------
        $this->accountTypes = DB::table('mas_account_type')
            ->where('mas_account_type_status_id', 1)
            ->orderBy('mas_account_type_name')
            ->get();

        // -------------------------------
        // NORMALIZE BANK DATA (KEY FIX)
        // -------------------------------
        $raw = $draft->data['bank'] ?? [];

        $this->bank = [
            'identity' => [
                'pan_number'    => $raw['identity']['pan_number']    ?? null,
                'pan_name'      => $raw['identity']['pan_name']      ?? null,
                'aadhar_number' => $raw['identity']['aadhar_number'] ?? null,
            ],

            'salary' => [
                'basic_pay'      => $raw['salary']['basic_pay']      ?? null,
                'salary_mode_id' => $raw['salary']['salary_mode_id']
                    ?? $raw['salary_mode_id']
                        ?? null,
            ],

            'account' => [
                'account_type_id' => $raw['account']['account_type_id'] ?? null,
                'account_number'  => $raw['account']['account_number']
                    ?? $raw['account_number']
                        ?? null,
                'account_name'    => $raw['account']['account_name']
                    ?? $raw['account_name']
                        ?? null,
                'ifs_code'       => $raw['account']['ifs_code']
                    ?? $raw['ifsc_code']
                        ?? null,
                'bank_name'       => $raw['account']['bank_name'] ?? null,
                'branch_name'     => $raw['account']['branch_name'] ?? null,
            ],
        ];

        // 🔥 Prepopulate bank + branch if IFSC already exists
        $ifsc = $this->bank['account']['ifs_code'] ?? null;

        if ($ifsc) {
            $this->updatedBankAccountIfsCode($ifsc);
        }
    }

    private function clearBankDetails(): void
    {
        $this->bank['account']['bank_name']   = null;
        $this->bank['account']['branch_name'] = null;
    }

    /* ================= SAVE ================= */

    public function save(): void
    {
        $this->validate([
            // IDENTITY
            'bank.identity.pan_number'    => 'required|alpha_num',
            'bank.identity.pan_name'      => 'required|string',
            'bank.identity.aadhar_number' => [
                'required',
                'regex:/^\d{4}-\d{4}-\d{4}$/'
            ],

            // SALARY
         //   'bank.salary.basic_pay'       => 'required|digits_between:1,10',
            'bank.salary.salary_mode_id'  => 'required|integer',

            // BANK
            'bank.account.account_type_id' => 'required|integer',
            'bank.account.account_number'  => 'required|digits_between:6,20',
            'bank.account.account_name'    => 'required|string',
            'bank.account.ifs_code'       => 'required|alpha_num|size:11',
            'bank.account.bank_name'       => 'required|string',
            'bank.account.branch_name'     => 'required|string',
        ]);

        $data = $this->draft->data;
        $data['bank'] = $this->bank;

        $completed = $this->draft->completed_tabs ?? [];
        if (! is_array($completed)) {
            $completed = json_decode($completed, true) ?: [];
        }

        if (! in_array('bank', $completed, true)) {
            $completed[] = 'bank';
        }

        $this->generateTinIfPossible();

        $this->draft->update([
            'data'           => $data,
            'completed_tabs' => $completed,
            'current_tab'    => 'documents',
        ]);

        $this->dispatch('switch-tab', tab: 'documents');
    }

    public function render()
    {
        return view('livewire.profile.tabs.bank-tab');
    }

    /* ================= IFSC LOOKUP ================= */

    public function updatedBankAccountIfsCode($value): void
    {
       // dd('loading updatedBankAccountIfscCode', $value);
        $ifsc = strtoupper(trim($value));

        $this->bank['account']['bank_name']   = null;
        $this->bank['account']['branch_name'] = null;

        if (strlen($ifsc) !== 11) {
            return;
        }

        $row = DB::table('mas_ifsc as i')
            ->join('mas_bank as b', 'b.id', '=', 'i.mas_ifsc_bank_id')
            ->join('mas_bank_branch as br', 'br.id', '=', 'i.mas_ifsc_branch_id')
            ->where('i.mas_ifsc_number', $ifsc)
            ->where('i.mas_ifsc_status_id', 1)
            ->where('b.mas_bank_status_id', 1)
            ->where('br.mas_bank_branch_status_id', 1)
            ->select([
                'b.mas_bank_name',
                'br.mas_bank_branch_branch_name',
                'i.mas_ifsc_bank_id','b.id','br.id',
            ])
            ->first();
// dd($ifsc, $row);
        if (! $row) {
            return;
        }

        $this->bank['account']['bank_name'] =
            strtoupper($row->mas_bank_name);

        $this->bank['account']['branch_name'] =
            strtoupper($row->mas_bank_branch_branch_name);
    }

    private function generateTinIfPossible(): void
    {
        $user = Auth::user();
      //  dd($user);
        // 🔒 HARD LOCK: Never regenerate if already issued
        if ($user->user_tin) {
            return;
        }

        $pan    = $this->bank['identity']['pan_number'] ?? null;
        $aadhar = $this->bank['identity']['aadhar_number'] ?? null;

        if (! $pan || ! $aadhar) {
            return;
        }

        $pan    = strtoupper(trim($pan));
        $aadhar = preg_replace('/[^0-9]/', '', $aadhar);

        if (strlen($pan) < 4 || strlen($aadhar) !== 12) {
            return;
        }

        // Get stream safely
        $streamName = $user->stream->mas_stream_name ?? null;
        if (! $streamName) return;

        $firstChar = strtoupper(substr($streamName, 0, 1));

        $tin = $firstChar
            . substr($aadhar, -4)
            . substr($pan, -4);
   //     dd($pan, $aadhar, $tin);
        DB::transaction(function () use ($user, $tin) {

            // Save TIN
            $user->update([
                'user_tin' => $tin,
            ]);

            // 🧾 Audit log
            UserTinAudit::create([
                'user_id'     => $user->id,
                'tin'         => $tin,
                'issued_from' => 'bank_tab',
            ]);
        });
    }

    public function updatedBankIdentityPanNumber(): void
    {
        $this->generateTinIfPossible();
    }

    public function updatedBankIdentityAadharNumber(): void
    {
        $this->generateTinIfPossible();
    }
}
