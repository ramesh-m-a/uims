<?php

namespace App\Providers;

use App\Models\Admin\Role;
use App\Observers\RoleObserver;
use App\Repositories\AllocationRepository;
use App\Repositories\BatchRepository;
use App\Repositories\TempAllocationRepository;
use App\Services\ExaminerAllocation\Builders\FreshPlanBuilder;
use App\Services\ExaminerAllocation\Domain\AllocationState;
use App\Services\ExaminerAllocation\Pickers\DatabaseExaminerPicker;
use App\Services\ExaminerAllocation\Pickers\ExaminerPicker;
use App\Services\ExaminerAllocation\Rules\CollisionRule;
use App\Services\ExaminerAllocation\Rules\DummyRule;
use App\Services\ExaminerAllocation\Rules\OrthopaedicsRule;
use App\Services\ExaminerAllocation\Rules\ReuseRule;
use App\Services\ExaminerAllocation\Rules\SchemeRule;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // repositories
        $this->app->singleton(TempAllocationRepository::class);
        $this->app->singleton(AllocationRepository::class);

        // picker binding
        $this->app->bind(ExaminerPicker::class, DatabaseExaminerPicker::class);

        // allocation state (new per request)
        $this->app->singleton(AllocationState::class, function () {
            return new AllocationState();
        });

        // FreshPlanBuilder binding (NO named params, fully safe)
        $this->app->bind(FreshPlanBuilder::class, function ($app) {

            // Create ONE shared state
            $state = new AllocationState();

            // Picker using same state
            $picker = new DatabaseExaminerPicker($state);

            // Rules that depend on state must also use same instance
            $reuseRule     = new ReuseRule($picker, $state);
            $collisionRule = new CollisionRule($state);

            return new FreshPlanBuilder(
                $app->make(BatchRepository::class),
                $picker,
                $state,
                $app->make(SchemeRule::class),
                $app->make(OrthopaedicsRule::class),
                [
                    $reuseRule,
                    $app->make(\App\Services\ExaminerAllocation\Rules\ClonePreferenceRule::class),
                    $collisionRule,
                    $app->make(\App\Services\ExaminerAllocation\Rules\InternalPromotionRule::class),
                    $app->make(\App\Services\ExaminerAllocation\Rules\ShortfallCarryForwardRule::class),
                    $app->make(DummyRule::class),
                ]
            );
        });
    }

    public function boot(): void
    {
        Role::observe(RoleObserver::class);
    }
}
