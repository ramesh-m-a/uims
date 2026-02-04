<?php

namespace App\Livewire\Master;

use App\Livewire\Base\TableBase;

abstract class MasterTableBase extends TableBase
{
    /* =====================================================
     | VALIDATION (unchanged behavior)
     ===================================================== */
    public function validateForm(): void
    {
        $this->validate(
            $this->rules(),
            method_exists($this, 'validationMessages')
                ? $this->validationMessages()
                : [],
            method_exists($this, 'validationAttributes')
                ? $this->validationAttributes()
                : []
        );
    }

    /**
     * Each master MUST define its config key
     * Example: master.common.stream
     */
    abstract protected function configKey(): string;

    public function __invoke()
    {
        return $this->render();
    }
}
