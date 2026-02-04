<?php

namespace App\Livewire\Admin\PermissionTemplate;

use App\Models\Admin\PermissionTemplate;
use Livewire\Component;

class TemplateTable extends Component
{
    public function render()
    {
        return view('livewire.admin.permission-template.template-table', [
            'templates' => PermissionTemplate::orderBy('name')->get(),
        ]);
    }
}
