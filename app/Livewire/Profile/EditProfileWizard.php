<?php

namespace App\Livewire\Profile;

use App\Models\Master\Common\Status;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\UserProfileDraft;
use App\Support\ProfileStatus;

class EditProfileWizard extends Component
{
    public UserProfileDraft $draft;

    /** Active tab key */
    public string $currentTab = 'basic';

    public function mount(): void
    {
        $user = auth()->user();

        $draftStatus = strtolower(Status::where('mas_status_code', 'Draft')->value('mas_status_code'));

        $this->draft = UserProfileDraft::firstOrCreate(
            ['user_id' => $user->id],
            ['status' => $draftStatus, 'data' => $this->emptyDraft(), 'current_tab' => 'basic', 'completed_tabs' => [],]);

        if (ProfileStatus::isLocked($this->draft)) {
            abort(403, 'Profile is locked');
        }

        $this->currentTab = $this->draft->current_tab ?? 'basic';
    }

    /**
     * ✅ LISTEN TO CHILD EVENTS (LIVEWIRE 3)
     */
    #[On('switch-tab')]
    public function switchTab(string $tab): void
    {
        // 🔥 FORCE UI UPDATE
        $this->currentTab = $tab;
    }

    public function render()
    {
        return view('livewire.profile.edit-profile-wizard');
    }

    protected function emptyDraft(): array
    {
        return [
            'basic' => [
                'dob' => null,
                'gender_id' => null,
                'father_name' => null,
                'religion_id' => null,
                'category_id' => null,
            ],
            'address' => [
                'same_address' => true,
                'permanent' => [
                    'address_1' => null,
                    'address_2' => null,
                    'district' => null,
                    'state_id' => null,
                    'pincode' => null,
                ],
                'temporary' => [
                    'address_1' => null,
                    'address_2' => null,
                    'district' => null,
                    'state_id' => null,
                    'pincode' => null,
                ],
            ],
            'qualification' => [],
            'work' => [],
            'bank' => [],
            'documents' => [],
        ];
    }

    protected $listeners = [
        'profileSubmitted' => 'redirectAfterSubmit',
    ];

    public function redirectAfterSubmit(): void
    {
        redirect()->route('my-details.index'); // 🔁 SAME AS PRODUCTION
    }


    #[On('goToTab')]
    public function goToTab($tab)
    {
        $this->currentTab = $tab;
    }
}
