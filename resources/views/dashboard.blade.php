@php
  //  dd(config()->all());

   // dd(config('dashboard'));
@endphp

@php
   /* dd(
        (new \App\Services\Dashboard\DashboardRegistry())
            ->build(auth()->user()->role ?? 'admin', [
                'college_id'   => auth()->user()->user_college_id ?? null,
                'principal_id' => auth()->id(),
            ])
    );*/
@endphp

{{--<x-layouts.app :title="__('Dashboardaadfafds')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
    </div>
</x-layouts.app>--}}



@php
    $dashboard = (new \App\Services\Dashboard\DashboardRegistry())
        ->build(auth()->user()->role ?? 'admin', [
            'college_id'   => auth()->user()->user_college_id ?? null,
            'principal_id' => auth()->id(),
        ]);
@endphp

<x-layouts.app :title="__('Dashboard')">

    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        {{-- ===================== KPIs ===================== --}}
        @if(!empty($dashboard['kpis']))
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($dashboard['kpis'] as $key => $kpi)
                    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 bg-white dark:bg-neutral-900">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $kpi['label'] }}
                        </div>

                        <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                            {{ $kpi['value'] }}
                        </div>

                        @if(!empty($kpi['route']))
                            <a href="{{ $kpi['route'] }}"
                               class="mt-3 inline-block text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                View →
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif


        {{-- ===================== QUEUES ===================== --}}
        @if(!empty($dashboard['queues']))
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900">
                <div class="p-4 border-b border-neutral-200 dark:border-neutral-700 font-semibold">
                    Queues
                </div>

                <div class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @foreach($dashboard['queues'] as $queue)
                        <div class="p-4">
                            <div class="font-medium mb-2 text-gray-800 dark:text-gray-200">
                                {{ $queue['label'] }}
                            </div>

                            @if($queue['value']->isEmpty())
                                <div class="text-sm text-gray-500">No records</div>
                            @else
                                <ul class="space-y-1 text-sm">
                                    @foreach($queue['value'] as $row)
                                        <li class="flex justify-between">
                                            <span>{{ $row->name ?? '—' }}</span>
                                            <span class="text-gray-400">#{{ $row->id }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif


        {{-- ===================== BREAKDOWNS ===================== --}}
        @if(!empty($dashboard['breakdowns']))
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach($dashboard['breakdowns'] as $key => $block)

                    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900">
                        <div class="p-4 border-b border-neutral-200 dark:border-neutral-700 font-semibold capitalize">
                            {{ $block['meta']['label'] ?? $key }}
                        </div>

                        <div class="p-4 overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                <tr class="text-left text-gray-500 border-b border-neutral-200 dark:border-neutral-700">
                                    <th class="py-2">ID</th>
                                    <th>Total</th>
                                    <th>Approved</th>
                                    <th>Pending</th>
                                    <th>Rejected</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($block['data'] as $row)
                                    <tr class="border-b border-neutral-100 dark:border-neutral-800">
                                        <td class="py-2">{{ collect($row)->first() }}</td>
                                        <td>{{ $row->total }}</td>
                                        <td>{{ $row->approved }}</td>
                                        <td>{{ $row->pending }}</td>
                                        <td>{{ $row->rejected }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 text-center text-gray-500">
                                            No data
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                @endforeach
            </div>
        @endif

    </div>
</x-layouts.app>
