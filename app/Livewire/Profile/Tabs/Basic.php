<?php

namespace App\Livewire\Profile\Tabs;

use App\Livewire\Profile\EditProfileWizard06;
use Livewire\Component;
use App\Models\UserProfileDraft;
use App\Support\AuditLogger;

class Basic extends Component
{
    public UserProfileDraft $draft;
    public array $data = [];

    public function mount()
    {
        $this->data = $this->draft->data['basic'] ?? [];
    }

    public function save()
    {
        $this->validate([
            'data.basic_details_gender_id' => 'required',
            'data.basic_details_dob' => 'required|date',
        ]);

        $original = $this->draft->data['basic'] ?? [];

        $draftData = $this->draft->data;
        $draftData['basic'] = $this->data;

        $this->draft->update(['data' => $draftData]);

        AuditLogger::log(
            table: 'user_profile_drafts',
            recordId: $this->draft->id,
            action: 'update',
            oldValues: $original,
            newValues: $this->data
        );

        $this->dispatch('tabCompleted', 'basic')
            ->to(EditProfileWizard06::class);
    }

    public function render()
    {
        return view('livewire.profile.tabs.basic');
    }
}
