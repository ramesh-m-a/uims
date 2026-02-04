<?php

namespace App\Livewire\Master\Concerns;

trait MasterDeleteBase
{
    /* ==========================
     | STATE
     ========================== */
    public bool $showDeleteModal = false;
    public ?int $deleteId = null;

    /* ==========================
     | CONFIRM DELETE
     ========================== */
    public function confirmDelete(int $id): void
    {
        abort_unless($this->can('delete'), 403);

        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    /* ==========================
     | DELETE CONFIRMED
     ========================== */
    public function deleteConfirmed(): void
    {
        abort_unless($this->can('delete'), 403);

        $this->modelClass::findOrFail($this->deleteId)->delete();

        $this->js(
            "Alpine.store('toast').push('success', 'Deleted successfully!')"
        );

        $this->showDeleteModal = false;
        $this->deleteId = null;

        $this->resetPage();
    }
}
