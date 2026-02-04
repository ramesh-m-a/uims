<?php

namespace App\Livewire\Master\Concerns;

trait MasterFormBase
{
    /* ==========================
     | STATE
     ========================== */
    public bool $showModal = false;
    public ?int $editingId = null;

    public array $form = [];

    /* ==========================
     | REQUIRED FROM CHILD
     ========================== */
    abstract protected function formDefaults(): array;
    abstract protected function rules(): array;

    /* ==========================
     | BREADCRUMB MODE (✅ FIX)
     ========================== */
    public function breadcrumbMode(): ?string
    {
        if (!$this->showModal) {
            return null;
        }

        return $this->editingId ? 'edit' : 'add';
    }

    /* ==========================
     | CREATE
     ========================== */
    public function create(): void
    {
        abort_unless($this->can('create'), 403);

        $this->editingId = null;
        $this->resetValidation();

        $this->form = $this->formDefaults();

        // Default Status if present
        if (array_key_exists('mas_stream_status_id', $this->form)) {
            $this->form['mas_stream_status_id'] = 1;
        }

        $this->showModal = true;
    }

    /* ==========================
     | EDIT
     ========================== */
    public function editRow(int $id): void
    {
        abort_unless($this->can('edit'), 403);

        $model = $this->modelClass::findOrFail($id);

        $this->editingId = $id;
        $this->resetValidation();

        $this->form = $model->only(array_keys($this->formDefaults()));

        $this->showModal = true;
    }

    /* ==========================
     | SAVE
     ========================== */
    public function saveold(): void
    {
        $this->validateForm();

        $data = $this->form;

        if ($this->editingId) {
            $data['updated_by'] = auth()->id();
        } else {
            $data['created_by'] = auth()->id();
        }

        $this->modelClass::updateOrCreate(
            ['id' => $this->editingId],
            $data
        );

        $message = $this->editingId
            ? 'Updated successfully!'
            : 'Created successfully!';

        $this->js("Alpine.store('toast').push('success', '{$message}')");

        $this->cancelEdit();
        $this->resetPage();
    }

    public function save(): void
    {
        $this->validateForm();

        $data = $this->form;

        if ($this->editingId) {
            $data['updated_by'] = auth()->id();

            // Normal update
            $model = $this->modelClass::findOrFail($this->editingId);
            $model->update($data);

        } else {
            $data['created_by'] = auth()->id();

            // 🔒 Find by unique business key instead of blindly inserting
            if (array_key_exists('mas_designation_name', $data)) {
                $model = $this->modelClass::where(
                    'mas_designation_name',
                    $data['mas_designation_name']
                )->first();
            } else {
                $model = null;
            }

            // If not found → create
            if (!$model) {
                $model = $this->modelClass::create($data);
            }
        }

        // 🔥 Always allow post-save hook
        if (method_exists($this, 'afterSave')) {
            $this->afterSave($model);
        }

        $message = $this->editingId
            ? 'Updated successfully!'
            : 'Saved successfully!';

        $this->js("Alpine.store('toast').push('success', '{$message}')");

        $this->cancelEdit();
        $this->resetPage();
    }

    /* ==========================
     | CANCEL
     ========================== */
    public function cancelEdit(): void
    {
        $this->showModal = false;
        $this->editingId = null;
        $this->resetValidation();
    }
}
