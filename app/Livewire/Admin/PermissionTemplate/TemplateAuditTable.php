<?php

namespace App\Livewire\Admin\PermissionTemplate;

use App\Models\Admin\PermissionChangeAudit;
use Livewire\Component;

class TemplateAuditTable extends Component
{
    public function render()
    {
        return view('livewire.admin.permission-template.template-audit-table', [
            'audits' => PermissionChangeAudit::latest()->paginate(20),
        ]);
    }
}
