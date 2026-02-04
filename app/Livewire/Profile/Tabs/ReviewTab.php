<?php

namespace App\Livewire\Profile\Tabs;

use App\Models\Admin\AdminRole;
use App\Models\Master\Common\Bank;
use App\Models\Master\Config\Academic\Department;
use App\Services\Profile\ProfileCommitService;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\UserProfileDraft;
use App\Support\ProfileStatus;
use Carbon\Carbon;

/* ===== Masters ===== */
use App\Models\Master\Common\Gender;
use App\Models\Master\Common\Religion;
use App\Models\Master\Common\Category;

use App\Models\Master\Config\Academic\Designation;
use App\Models\Master\Config\Academic\Degree;
use App\Models\Master\Config\Academic\Specialisation;


class ReviewTab extends Component
{
    public UserProfileDraft $draft;

    /* ===== View Models expected by Blade ===== */
    public string $genderName = '-';
    public ?int $calculatedAge = null;
    public array $qualificationView = [];
    public array $workView = [];

    public function mount(UserProfileDraft $draft): void
    {
        $this->draft = $draft;

        if (ProfileStatus::isLocked($draft)) {
            abort(403, 'Profile already submitted / locked');
        }

        $this->buildViewModels();
    }

    private function buildViewModels(): void
    {
        $data = $this->draft->data ?? [];

     //   dd($data);

        /* ================= BASIC ================= */
        $genderId = data_get($data, 'basic.gender_id');
        $this->genderName =
            Gender::where('id', $genderId)->value('mas_gender_name') ?? '-';

        $religionId = data_get($data, 'basic.religion_id');
        $data['basic']['religion_name'] =
            Religion::where('id', $religionId)->value('mas_religion_name') ?? '-';

        $categoryId = data_get($data, 'basic.category_id');
        $data['basic']['category_name'] =
            Category::where('id', $categoryId)->value('mas_category_name') ?? '-';

        $designationId = data_get($data, 'basic.designation_id');
        $data['basic']['designation_name'] =
            Designation::where('id', $designationId)->value('mas_designation_name') ?? '-';

        $deptId = data_get($data, 'basic.department_id');
        $data['basic']['department_name'] =
            Department::where('id', $deptId)->value('mas_department_name') ?? '-';
      //  dd($designationId, $deptId);
        $adminId = data_get($data, 'basic.administrative_id');
        $data['basic']['admin_role_name'] =
            AdminRole::where('id', $adminId)->value('mas_admin_role_name') ?? '-';

        /* ================= AGE ================= */
        $dob = data_get($data, 'basic.dob');
        if ($dob) {
            try {
                $this->calculatedAge = Carbon::parse($dob)->age;
            } catch (\Throwable) {}
        }

        /* ================= QUALIFICATION VIEW ================= */
        $this->qualificationView = collect(data_get($data, 'qualification', []))
            ->map(function ($q) {
                return [
                    'degree' =>
                        Degree::where('id', $q['degree_id'] ?? null)
                            ->value('mas_degree_name') ?? '-',

                    'specialisation' =>
                        Specialisation::where('id', $q['specialisation_id'] ?? null)
                            ->value('mas_specialisation_name') ?? '-',

                    'institution'   => $q['institution'] ?? '-',
                    'year_exam'     => $q['year_of_exam'] ?? '-',
                    'year_award'    => $q['year_of_award'] ?? '-',
                    'reg_no'        => $q['registration_no'] ?? '-',
                ];
            })
            ->toArray();

        /* ================= WORK VIEW ================= */
        $this->workView = collect(data_get($data, 'work', []))
            ->map(function ($w) {
                return [
                    'designation' =>
                        Designation::where('id', $w['designation_id'] ?? null)
                            ->value('mas_designation_name') ?? '-',

                    'institution' =>
                        $w['institution']
                        ?? $w['institution_name']
                            ?? '-',

                    'from' => $w['from_date'] ?? '-',
                    'to'   => !empty($w['is_current']) ? 'Present' : ($w['to_date'] ?? '-'),
                ];
            })
            ->toArray();

        /* ================= BANK ================= */
        $bankId   = data_get($data, 'bank.account.bank_id');
        $branchId = data_get($data, 'bank.account.branch_id');

        $data['bank']['account']['bank_name'] =
            Bank::where('id', $bankId)->value('mas_bank_name') ?? '-';

      /*  $data['bank']['account']['branch_name'] =
            Branch::where('id', $branchId)->value('branch_name') ?? '-';*/

        /* ================= WRITE BACK ================= */
        $this->draft->data = $data;
    }

    public function getIsCompleteProperty(): bool
    {
        $data = $this->draft->data ?? [];

        return
            filled(data_get($data, 'basic.dob')) &&
            filled(data_get($data, 'address.permanent.address_1')) &&
            count(data_get($data, 'qualification', [])) > 0 &&
            count(data_get($data, 'work', [])) > 0 &&
            filled(data_get($data, 'bank.account.ifsc_code')) &&
            count(data_get($data, 'documents', [])) > 0;
    }

    public function submit()
    {
        if (! $this->isComplete) {
            $this->addError('submit', 'Profile is incomplete.');
            return;
        }

        app(ProfileCommitService::class)->commit(auth()->user(), $this->draft);

        return redirect()
            ->route('my-details.index')
            ->with('success', 'Profile submitted successfully.');
    }

    public function render()
    {
        return view('livewire.profile.tabs.review-tab', [
            'qualificationView' => $this->qualificationView,
            'workView'          => $this->workView,
        ]);
    }

    public function goBack(): void
    {
        $this->dispatch('goToTab', tab: 'documents');
    }

}
