<?php

namespace App\Services\ExaminerAllocation\Builders;

use App\Services\ExaminerAllocation\AllocationContext;
use App\Services\ExaminerAllocation\AllocationResult;
use App\Services\ExaminerAllocation\Domain\AllocationState;
use App\Services\ExaminerAllocation\DTO\SlotDTO;
use App\Services\ExaminerAllocation\DTO\AllocationRowDTO;
use App\Services\ExaminerAllocation\Pickers\ExaminerPicker;
use App\Repositories\BatchRepository;
use App\Services\ExaminerAllocation\Rules\OrthopaedicsRule;
use App\Services\ExaminerAllocation\Rules\SchemeRule;

class FreshPlanBuilder
{
    public function __construct(
        protected BatchRepository $batchRepo,
        protected ExaminerPicker $picker,
        protected AllocationState $state,
        protected SchemeRule $schemeRule,
        protected OrthopaedicsRule $orthoRule,
        protected array $rules = [],
    ) {}

    public function build(AllocationContext $context): AllocationResult
    {
        $ranges = $this->batchRepo->rangesForScope(
            $context->yearId,
            $context->monthId,
            $context->schemeId,
            $context->degreeId,
            $context->streamId,
        );

        $ranges = collect($ranges)
            ->sortBy(fn($r) => $r->from_date)
            ->values();

        $rows = collect();
        $shortfalls = [];
        $globalUsed = [];

        foreach ($ranges as $range) {

            $contextSlots = [];

            foreach ($this->requiredSlots($range) as $slotType) {

                $slot = $this->buildSlot(
                    slotType: $slotType,
                    range: $range,
                    globalUsed: $globalUsed,
                    contextSlots: $contextSlots
                );

                $contextSlots[$slotType] = $slot;

                if (!$slot->isFilled()) {
                    $shortfalls[] = "{$slotType} shortfall on {$range->from_date}";
                }

                $rows->push(
                    new AllocationRowDTO(
                        examinerId: $slot->examinerId ?? 0,
                        name: $slot->name ?? 'Assigned',
                        mobile: $slot->mobile ?? '',
                        status: $slot->status,
                        colour: $slot->colour,

                        batchName: $range->batch_name,
                        subjectName: $range->subject_name,
                        centreName: $range->centre_name,

                        fromDate: $range->from_date,
                        toDate: $range->to_date,

                        rangeId: $range->id,
                        batchId: $range->batch_id,

                        yearId: $context->yearId,
                        monthId: $context->monthId,
                        schemeId: $context->schemeId,
                        degreeId: $context->degreeId,

                        subjectId: $range->subject_id,
                        centreId: $range->centre_id,

                        examinerType: $slotType,
                        examinerTypeId: str_contains($slotType, 'Internal') ? 1 : 2,
                        statusId: 26
                    )
                );

                if ($slot->examinerId) {
                    $globalUsed[$range->from_date][] = $slot->examinerId;

                    $this->state->record(
                        $this->state->seriesKey(
                            $range->batch_id,
                            $range->subject_id,
                            $range->centre_id
                        ),
                        $range->from_date,
                        $slotType,
                        $slot->examinerId,
                        $slot->name,
                        $slot->mobile
                    );
                }
            }
        }

        return new AllocationResult(
            rows: $rows,
            statuses: collect($rows)->pluck('status')->unique()->values()->all(),
            shortfalls: $shortfalls
        );
    }

    protected function buildSlot(
        string $slotType,
        object $range,
        array &$globalUsed,
        array $contextSlots
    ): SlotDTO {

        $exclude = empty($globalUsed)
            ? []
            : array_unique(array_merge(...array_values($globalUsed)));

        $seriesKey = $this->state->seriesKey(
            $range->batch_id,
            $range->subject_id,
            $range->centre_id
        );

        $slot = new SlotDTO(type: $slotType);

        // 1. RULE PIPELINE
        foreach ($this->rules as $rule) {
            if ($rule->applies(['range' => $range])) {
                $slot = $rule->process($slot, [
                    'range'     => $range,
                    'seriesKey' => $seriesKey,
                ]);

                if ($slot->isFilled()) {
                    $this->state->record(
                        $seriesKey,
                        $range->from_date,
                        $slotType,
                        $slot->examinerId,
                        $slot->name,
                        $slot->mobile
                    );

                    return $slot;
                }
            }
        }

        // 2. HARD BUSINESS RULE:
        // If this is NOT first day and Day-1 had no examiner → block picker
        if ($this->state->hasSeries($seriesKey)) {

            $dates = $this->state->getSeriesDates($seriesKey);
            sort($dates);

            $firstDate = $dates[0] ?? null;

            if ($firstDate && $firstDate !== $range->from_date) {

                $day1 = $this->state->getSlotAssignments(
                    $seriesKey,
                    $firstDate,
                    $slotType
                );

                $day1ExaminerId = $day1[0] ?? null;

                if (!$day1ExaminerId) {
                    return new SlotDTO(type: $slotType);
                }
            }
        }

        // 3. FINAL FALLBACK → PICKER (ONLY IF ALLOWED)
        $picked = $this->picker->pick(
            slotType: $slotType,
            departmentId: $range->department_id,
            centreId: $range->centre_id,
            attachedCentreIds: $range->attached_ids,
            excludeExaminerIds: $exclude,
            date: $range->from_date
        );

        if ($picked) {
            $slot = new SlotDTO(
                type: $slotType,
                examinerId: $picked['id'],
                name: $picked['name'],
                mobile: $picked['mobile'],
                status: $picked['status'],
                colour: $picked['colour']
            );

            $this->state->record(
                $seriesKey,
                $range->from_date,
                $slotType,
                $slot->examinerId,
                $slot->name,
                $slot->mobile
            );

            return $slot;
        }

        return new SlotDTO(type: $slotType);
    }

    protected function requiredSlots(object $range): array
    {
        $orthoSlots = $this->orthoRule->slotsFor($range);
        if ($orthoSlots !== null) {
            return $orthoSlots;
        }

        return $this->schemeRule->slotsFor($range);
    }
}
