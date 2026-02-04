<?php

namespace App\Traits;

use App\Enums\StatusEnum;
use App\Exceptions\InvalidStatusTransition;
use Illuminate\Support\Facades\DB;

trait HasStatusWorkflow
{
    /**
     * Transition rules:
     * TO => [allowed FROM states]
     */
    protected array $transitions = [

        StatusEnum::PROFILE_COMPLETED => [
            StatusEnum::DRAFT,
            StatusEnum::UPDATE_BASIC,
            StatusEnum::UPLOAD_PHOTO,
        ],

        StatusEnum::SUBMITTED_TO_PRINCIPAL => [
            StatusEnum::PROFILE_COMPLETED,
        ],

        StatusEnum::APPLICATION_APPROVED_COLLEGE => [
            StatusEnum::SUBMITTED_TO_PRINCIPAL,
        ],

        StatusEnum::APPLICATION_REJECTED_COLLEGE => [
            StatusEnum::SUBMITTED_TO_PRINCIPAL,
        ],

        StatusEnum::APPLICATION_APPROVED_RGUHS => [
            StatusEnum::APPLICATION_APPROVED_COLLEGE,
        ],

        StatusEnum::APPLICATION_REJECTED_RGUHS => [
            StatusEnum::APPLICATION_APPROVED_COLLEGE,
        ],

        StatusEnum::NEED_MORE_INFO => [
            StatusEnum::SUBMITTED_TO_PRINCIPAL,
            StatusEnum::APPLICATION_APPROVED_COLLEGE,
        ],
    ];

    /**
     * UI-safe guard (use this to show/hide buttons)
     */
    public function canTransitionTo(string $toStatus): bool
    {
        $current = $this->status?->mas_status_code;

        if (! isset($this->transitions[$toStatus])) {
            return true;
        }

        return in_array($current, $this->transitions[$toStatus], true);
    }

    /**
     * Core guarded transition
     */
    protected function transitionTo(string $toStatus): void
    {
        $current = $this->status?->mas_status_code;

        if (! $this->canTransitionTo($toStatus)) {
            throw new InvalidStatusTransition($current, $toStatus);
        }

        // Update actual status
        $this->setStatus($toStatus);

        // Audit log (matches your real table)
        $this->logStatusTransition($current, $toStatus);

        // Event / Notification hooks
        $this->afterStatusChanged($current, $toStatus);
    }

    /**
     * Audit logging using your audit_logs schema
     */
    protected function logStatusTransition(?string $from, string $to): void
    {
        DB::table('audit_logs')->insert([
            'user_id'    => auth()->id(),
            'table_name' => $this->getTable(),
            'record_id'  => $this->getKey(),
            'action'     => 'update',
            'old_values' => json_encode(['status' => $from]),
            'new_values' => json_encode(['status' => $to]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Central hook for domain events & notifications
     * You can attach listeners later without touching this file.
     */
    protected function afterStatusChanged(?string $from, string $to): void
    {
        match ($to) {

            StatusEnum::SUBMITTED_TO_PRINCIPAL =>
            event(new \App\Events\ProfileSubmitted($this)),

            StatusEnum::APPLICATION_APPROVED_COLLEGE =>
            event(new \App\Events\ApplicationApprovedByCollege($this)),

            StatusEnum::APPLICATION_APPROVED_RGUHS =>
            event(new \App\Events\ApplicationApprovedByRGUHS($this)),

            StatusEnum::APPLICATION_REJECTED_RGUHS =>
            event(new \App\Events\ApplicationRejectedByRGUHS($this)),

            default => null,
        };
    }

    /**
     * Export workflow diagram (Graphviz DOT format)
     * Paste output into https://dreampuf.github.io/GraphvizOnline
     */
    public function exportWorkflowDot(): string
    {
        $lines = ["digraph Workflow {"];

        foreach ($this->transitions as $to => $fromList) {
            foreach ($fromList as $from) {
                $lines[] = "  \"{$from}\" -> \"{$to}\";";
            }
        }

        $lines[] = "}";

        return implode("\n", $lines);
    }

    /* ================= BUSINESS METHODS ================= */

    public function markProfileCompleted(): void
    {
        $this->transitionTo(StatusEnum::PROFILE_COMPLETED);
    }

    public function submitProfile(): void
    {
        $this->transitionTo(StatusEnum::SUBMITTED_TO_PRINCIPAL);
    }

    public function approveByCollege(): void
    {
        $this->transitionTo(StatusEnum::APPLICATION_APPROVED_COLLEGE);
    }

    public function rejectByCollege(): void
    {
        $this->transitionTo(StatusEnum::APPLICATION_REJECTED_COLLEGE);
    }

    public function approveByRGUHS(): void
    {
        $this->transitionTo(StatusEnum::APPLICATION_APPROVED_RGUHS);
    }

    public function rejectByRGUHS(): void
    {
        $this->transitionTo(StatusEnum::APPLICATION_REJECTED_RGUHS);
    }

    public function requestMoreInfo(): void
    {
        $this->transitionTo(StatusEnum::NEED_MORE_INFO);
    }
}
