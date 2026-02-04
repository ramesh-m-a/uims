@php
    /**
     * Golden Breadcrumb Contract (FINAL)
     *
     * Inputs:
     *  - $title (string)   Optional
     *  - $mode  (add|edit) Optional
     *
     * Route is auto-detected and parsed
     */
    $route = $route
        ?? ($this->pageRoute ?? null)
        ?? request()->route()?->getName();
@endphp

<nav class="breadcrumb-container">
    <ol class="breadcrumb">

        {{-- HOME --}}
        <li class="breadcrumb-item">
            <i class="fa fa-home"></i>
            <a href="{{ route('dashboard') }}">Home</a>
        </li>

        {{-- =========================
         | MASTER → CONFIG → GROUP
         ========================= --}}
        @if($route && str_starts_with($route, 'master.config.'))
            @php
                // Example:
                // master.config.academic.stream.index
                $parts = explode('.', $route);
                $group = ucfirst($parts[2] ?? null);
            @endphp

            <li class="breadcrumb-item">Master</li>
            <li class="breadcrumb-item">Config</li>

            @if($group)
                <li class="breadcrumb-item">{{ $group }}</li>
            @endif

            {{-- =========================
             | LEGACY: MASTER → COMMON
             ========================= --}}
        @elseif($route && str_starts_with($route, 'master.common.'))
            <li class="breadcrumb-item">Master</li>
            <li class="breadcrumb-item">Common</li>

            {{-- =========================
             | LEGACY: MASTER ROOT
             ========================= --}}
        @elseif($route && str_starts_with($route, 'master.'))
            <li class="breadcrumb-item">Master</li>
        @endif

        {{-- =========================
         | CURRENT PAGE
         ========================= --}}
        <li class="breadcrumb-item active">
            {{ $title ?? 'List' }}
        </li>

        {{-- =========================
         | ADD / EDIT MODE
         ========================= --}}
        @if($mode === 'add')
            <li class="breadcrumb-item active">Add</li>
        @elseif($mode === 'edit')
            <li class="breadcrumb-item active">Edit</li>
        @endif

    </ol>
</nav>
