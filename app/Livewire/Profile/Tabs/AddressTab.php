<?php

namespace App\Livewire\Profile\Tabs;

use App\Models\Master\Common\State;
use Livewire\Component;
use App\Models\UserProfileDraft;

class AddressTab extends Component
{
    public $states = [];

    public UserProfileDraft $draft;

    public array $address = [
        'same_address' => true,

        'permanent' => [
            'address_1' => null,
            'address_2' => null,
            'address_3' => null,
            'district'  => null,
            'state_id'  => null,
            'pincode'   => null,
        ],

        'temporary' => [
            'address_1' => null,
            'address_2' => null,
            'address_3' => null,
            'district'  => null,
            'state_id'  => null,
            'pincode'   => null,
        ],
    ];

    /* ================= MOUNT ================= */

    public function mount(UserProfileDraft $draft): void
    {
        $this->draft = $draft;

        // Load states once (reuse master table)
        $this->states = State::orderBy('mas_state_name')
            ->get(['id', 'mas_state_name'])
            ->toArray();

        $saved = $draft->data['address'] ?? [];

        $this->address = array_replace_recursive($this->address, $saved);
    }

    /* ================= WATCHER ================= */

    /* ================= SAVE ================= */

    public function save(): void
    {
        if ($this->address['same_address']) {
            $this->address['temporary'] = $this->address['permanent'];
        }

        $this->validate([
            'address.permanent.address_1' => 'required|string',
     //       'address.permanent.district'  => 'required|string',
     //       'address.permanent.state_id'  => 'required',
     //       'address.permanent.pincode'   => 'required',
        ]);

        $data = $this->draft->data;
        $data['address'] = $this->address;

        $completed = $this->draft->completed_tabs ?? [];

        if (! in_array('address', $completed)) {
            $completed[] = 'address';
        }

        $this->draft->update([
            'data'           => $data,
            'completed_tabs' => $completed,
            'current_tab'    => 'qualification',
        ]);

        // ✅ MOVE UI FORWARD
        $this->dispatch('switch-tab', tab: 'qualification');
    }

    /* ================= RENDER ================= */

    public function render()
    {
        return view('livewire.profile.tabs.address-tab');
    }

    public function updated($name, $value)
    {
        // 🔁 When permanent address changes
        if (
            str_starts_with($name, 'address.permanent.')
            && ($this->address['same_address'] ?? false)
        ) {
            $this->address['temporary'] = $this->address['permanent'];
        }

        // 🔁 When Same as Above is toggled ON
        if ($name === 'address.same_address' && $value === true) {
            $this->address['temporary'] = $this->address['permanent'];
        }

        // 🔁 When Same as Above is toggled OFF
        if ($name === 'address.same_address' && $value === false) {
            $this->address['temporary'] = [
                'address_1' => null,
                'address_2' => null,
                'address_3' => null,
                'district'  => null,
                'state_id'  => null,
                'pincode'   => null,
            ];
        }
    }

}
