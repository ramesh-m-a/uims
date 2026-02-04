<?php

namespace App\Livewire\Base;

use Livewire\Component;

abstract class NonMasterTableBase extends Component
{
    /**
     * Override this in child if Add/Edit breadcrumb is needed
     */
    protected bool $supportsAddEditBreadcrumb = false;

    /**
     * Child controls WHEN add/edit is active
     */
    protected function isAddMode(): bool
    {
        return false;
    }

    protected function isEditMode(): bool
    {
        return false;
    }

    public function breadcrumbMode(): ?string
    {
        if (!$this->supportsAddEditBreadcrumb) {
            return null;
        }

        if ($this->isEditMode()) {
            return 'edit';
        }

        if ($this->isAddMode()) {
            return 'add';
        }

        return null;
    }
}
