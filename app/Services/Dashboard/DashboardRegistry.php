<?php

namespace App\Services\Dashboard;

use App\Services\Dashboard\Support\DashboardQueryBuilder;

class DashboardRegistry
{
    public function for(string $role): array
    {
        return config("dashboard.dashboard.roles.$role") ?? [];
    }

    protected function buildUsers($qb, array $config): array
    {
        return collect($config)
            ->filter(fn($meta) =>
                empty($meta['permission']) || auth()->user()->can($meta['permission'])
            )
            ->map(function ($meta) use ($qb) {

                $cacheKey = 'dashboard:bd:' . md5(json_encode($meta));
                $ttl = $meta['cache'] ?? 0;

                $data = $ttl > 0
                    ? cache()->remember($cacheKey, $ttl, fn() =>
                    $qb->usersBreakdown($meta['column'])->get()
                    )
                    : $qb->usersBreakdown($meta['column'])->get();

                return [
                    'meta' => $meta,
                    'data' => $data,
                ];
            })->toArray();
    }

    protected function buildPrincipal($qb, array $config, int $principalId): array
    {
        return collect($config)->map(function ($meta, $key) use ($qb, $principalId) {
            return [
                'meta' => $meta,
                'data' => $qb->principalBreakdown($meta['column'], $principalId)->get(),
            ];
        })->toArray();
    }

    public function build(string $role, array $context = []): array
    {
        $config = config("dashboard.dashboard.roles.$role");
        $qb = new DashboardQueryBuilder();

        return [
            'kpis' => $this->buildSection($config['kpis'] ?? [], $qb, $context),
            'queues' => $this->buildSection($config['queues'] ?? [], $qb, $context),
            'breakdowns' => $this->buildBreakdowns($role, $context),
        ];
    }

    protected function buildSection(array $items, $qb, array $context): array
    {
        return collect($items)->filter(function ($meta) {
            // Permission check
            if (!empty($meta['permission'])) {
                return auth()->user()?->can($meta['permission']);
            }
            return true;
        })->map(function ($meta) use ($qb, $context) {

            $cacheKey = 'dashboard:' . md5(json_encode([$meta, $context]));
            $ttl = $meta['cache'] ?? 0;

            $value = $ttl > 0
                ? cache()->remember($cacheKey, $ttl, fn() =>
                $qb->resolve($meta['query'], $meta, $context)
                )
                : $qb->resolve($meta['query'], $meta, $context);

            return [
                'label' => $meta['label'],
                'value' => $value,
                'route' => $meta['route'] ?? null,
            ];
        })->toArray();
    }


    protected function buildBreakdowns(string $role, array $context): array
    {
        $config = config("dashboard.dashboard.roles.$role.breakdowns") ?? [];

        if (empty($config)) return [];

        $qb = new \App\Services\Dashboard\Support\DashboardQueryBuilder();

        // College scoping
        if ($role === 'college' && !empty($context['college_id'])) {
            $qb->filterByCollege($context['college_id']);
        }

        // Principal logic
        if ($role === 'principal') {
            return collect($config)
                ->filter(fn($meta) =>
                    empty($meta['permission']) || auth()->user()->can($meta['permission'])
                )
                ->map(function ($meta) use ($qb, $context) {

                    $cacheKey = 'dashboard:bd:principal:' . md5(json_encode([$meta, $context]));
                    $ttl = $meta['cache'] ?? 0;

                    $data = $ttl > 0
                        ? cache()->remember($cacheKey, $ttl, fn() =>
                        $qb->principalBreakdown(
                            $meta['column'],
                            $context['principal_id']
                        )->get()
                        )
                        : $qb->principalBreakdown(
                            $meta['column'],
                            $context['principal_id']
                        )->get();

                    return [
                        'meta' => $meta,
                        'data' => $data,
                    ];
                })->toArray();
        }

        // Admin + College logic
        return collect($config)
            ->filter(fn($meta) =>
                empty($meta['permission']) || auth()->user()->can($meta['permission'])
            )
            ->map(function ($meta) use ($qb) {

                $cacheKey = 'dashboard:bd:user:' . md5(json_encode($meta));
                $ttl = $meta['cache'] ?? 0;

                $data = $ttl > 0
                    ? cache()->remember($cacheKey, $ttl, fn() =>
                    $qb->usersBreakdown($meta['column'])->get()
                    )
                    : $qb->usersBreakdown($meta['column'])->get();

                return [
                    'meta' => $meta,
                    'data' => $data,
                ];
            })->toArray();
    }
}
