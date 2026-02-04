<?php

namespace App\Livewire\Profile\Tabs;

use App\Models\Master\Common\Document;
use App\Models\UserProfileDraft;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentTab extends Component
{
    use WithFileUploads;

    public UserProfileDraft $draft;

    /** uploaded temp file */
    public $documentFile;

    public ?string $previewUrl = null;

    /** document_id => [file_path, original_name] */
    public array $documents = [];

    /** mas_document_id => label */
    public array $requiredDocuments = [];

    /** controls sequential upload */
    public int $activeDocumentId = 0;

    public function mount(UserProfileDraft $draft): void
    {
        $this->draft = $draft;

        // ✅ ALWAYS take designation from real user, not draft
        $user = $draft->user ?? auth()->user();
        $designationId = $user?->user_designation_id;

        /**
         * STEP 1: resolve designation
         * Adjust this path if your draft stores designation elsewhere
         */

        /**
         * STEP 2: load documents:
         * - globally required
         * - OR required for this designation
         */
        $this->requiredDocuments = Document::query()
            ->where('mas_document_status_id', 1)
            ->where(function ($q) use ($designationId) {
                $q->where('mas_document_is_required_global', 1);

                if ($designationId) {
                    $q->orWhereHas('designations', function ($dq) use ($designationId) {
                        $dq->where('mas_designation_id', $designationId)
                            ->where('mas_document_is_required', 1);
                    });
                }
            })
            ->orderBy('mas_document_sort_order')
            ->pluck('mas_document_name', 'id')
            ->toArray();
// dd($this->requiredDocuments);
        /**
         * STEP 3: load existing uploaded docs from draft
         */
        $docs = data_get($draft->data, 'documents', []);
        $this->documents = is_array($docs) ? $docs : [];

        /**
         * STEP 4: set first pending as active
         */
        foreach ($this->requiredDocuments as $docId => $label) {
            if (! isset($this->documents[$docId])) {
                $this->activeDocumentId = (int) $docId;
                break;
            }
        }
    }

    /* ===================== UPLOAD ===================== */

    public function uploadDocument(int $docId): void
    {
        if ($docId !== $this->activeDocumentId) {
            return;
        }

        $this->validate([
            'documentFile' => 'required|file|max:5120',
        ]);

        $path = $this->documentFile->store('documents', 'public');

        $this->documents[$docId] = [
            'file_path'     => $path,
            'original_name' => $this->documentFile->getClientOriginalName(),
        ];

        $data = $this->draft->data;
        $data['documents'] = $this->documents;

        $this->draft->update([
            'data' => $data,
        ]);

        $this->reset('documentFile');

        // Move to next pending
        foreach ($this->requiredDocuments as $nextId => $label) {
            if (! isset($this->documents[$nextId])) {
                $this->activeDocumentId = (int) $nextId;
                return;
            }
        }

        $this->activeDocumentId = 0;
    }

    /* ===================== SAVE ===================== */

    public function save(): void
    {
        foreach ($this->requiredDocuments as $docId => $label) {
            if (! isset($this->documents[$docId])) {
                $this->addError('documents', "$label is required.");
                return;
            }
        }

        $data = $this->draft->data;
        $data['documents'] = $this->documents;

        $completed = $this->draft->completed_tabs ?? [];
        if (! in_array('documents', $completed)) {
            $completed[] = 'documents';
        }

        $this->draft->update([
            'data'           => $data,
            'completed_tabs' => $completed,
            'current_tab'    => 'review',
        ]);

        $this->dispatch('switch-tab', tab: 'review');
    }

    public function render()
    {
        return view('livewire.profile.tabs.document-tab');
    }

    public function deleteDocument(int $docId): void
    {
        $data = $this->draft->data ?? [];

        // If document does not exist, silently ignore
        if (! isset($data['documents'][$docId])) {
            return;
        }

        // Delete physical file (optional but recommended)
        $filePath = $data['documents'][$docId]['file_path'] ?? null;

        if ($filePath && \Storage::disk('public')->exists($filePath)) {
            \Storage::disk('public')->delete($filePath);
        }

        // Remove from draft data
        unset($data['documents'][$docId]);

        // Persist back to DB
        $this->draft->update([
            'data' => $data,
        ]);

        // Refresh local state
        $this->documents = $data['documents'] ?? [];

        session()->flash('success', 'Document deleted successfully.');
    }

    public function selectDocument(int $docId): void
    {
        $this->activeDocumentId = $docId;
    }

}
