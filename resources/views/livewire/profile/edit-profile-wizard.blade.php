<div class="space-y-6">

    {{-- =========================
     | BREADCRUMB BAR
     ========================= --}}
    <div class="breadcrumb-bar">
        @include('partials.breadcrumb', [
            'title' => 'My Details',
            'mode'  => 'View / Update',
        ])
    </div>

    {{-- =========================
     | PAGE TITLE
     ========================= --}}
    <x-sub-header title="My Details" subtitle="View / Update">
    </x-sub-header>

    {{-- =========================
     | STEP TABS (PRODUCTION PARITY)
     ========================= --}}
    @php
        $tabs = [
            'basic'         => 'Basic',
            'address'       => 'Address',
            'qualification' => 'Qualification',
            'work'          => 'Work',
            'bank'          => 'Bank',
            'documents'     => 'Documents',
            'review'        => 'Review',
        ];

        $completed = $draft->completed_tabs ?? [];
    @endphp

    <div class="border-b pb-4">
        <div
            class="
                flex flex-wrap justify-center
                gap-4
                items-center
            "
        >
            @foreach ($tabs as $key => $label)
                @php
                    $isActive    = $currentTab === $key;
                    $isCompleted = in_array($key, $completed);
                    $isClickable = $isActive || $isCompleted;
                @endphp

                <button
                    @if($isClickable)
                        wire:click="$set('currentTab','{{ $key }}')"
                    @else
                        disabled
                    @endif
                    class="
                        min-w-[120px]
                        h-10
                        px-6
                        rounded-lg
                        text-sm
                        font-medium
                        flex items-center justify-center
                        transition-all
                        {{ $isActive
                            ? 'bg-blue-600 text-white shadow'
                            : ($isClickable
                                ? 'bg-gray-100 text-gray-800 hover:bg-gray-200'
                                : 'bg-gray-200 text-gray-400 cursor-not-allowed')
                        }}
                    "
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- =========================
     | TAB CONTENT
     ========================= --}}
    <div class="pt-6">

        @if ($currentTab === 'basic')
            <livewire:profile.tabs.basic-tab :draft="$draft" />
        @endif

        @if ($currentTab === 'address')
            <livewire:profile.tabs.address-tab :draft="$draft" />
        @endif

        @if ($currentTab === 'qualification')
            <livewire:profile.tabs.qualification-tab :draft="$draft" />
        @endif

        @if ($currentTab === 'work')
            <livewire:profile.tabs.work-tab :draft="$draft" />
        @endif

        @if ($currentTab === 'bank')
            <livewire:profile.tabs.bank-tab :draft="$draft" />
        @endif

        @if ($currentTab === 'documents')
            <livewire:profile.tabs.document-tab :draft="$draft" />
        @endif

        @if ($currentTab === 'review')
            <livewire:profile.tabs.review-tab :draft="$draft" />
        @endif

    </div>

</div>
