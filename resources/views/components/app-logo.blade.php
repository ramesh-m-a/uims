<div class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
    {{--<x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />--}}

    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 100 100"
        preserveAspectRatio="xMidYMid meet"
        {{ $attributes }}
    >
        <image
            href="{{ asset('images/RGUHS-logo-AA.png') }}"
            x="0"
            y="0"
            width="100"
            height="100"
            preserveAspectRatio="xMidYMid meet"
        />
    </svg>
</div>
<div class="ms-1 grid flex-1 text-start text-sm">
    <span class="mb-0.5 truncate leading-tight font-semibold">RGUHS</span>
</div>
