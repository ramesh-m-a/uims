<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class SidebarUserAvatar extends Component
{
    protected $listeners = [
        'profile-photo-updated' => '$refresh',
    ];

    public function render()
    {
        return view('livewire.sidebar-user-avatar', [
            'user' => Auth::user()?->fresh(),
        ]);
    }
}
