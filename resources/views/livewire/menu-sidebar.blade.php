{{-- resources/views/livewire/menu-sidebar.blade.php --}}
<div class="livewire-menu-sidebar-root">
    <div class="livewire-menu-sidebar">
        <flux:navlist variant="outline">
            @foreach($menus as $group)
                <flux:navlist.group :heading="__($group['title'])" class="grid">
                    <ul class="nav p-0 m-0 list-none">
                        @foreach($group['items'] as $item)
                            @include('livewire._menu_item', ['item' => $item])
                        @endforeach
                    </ul>
                </flux:navlist.group>
            @endforeach
        </flux:navlist>
    </div>

    <!-- Inline CSS (still inside single root element) -->
    <style>
        /* scope strongly to this sidebar root */
        .livewire-menu-sidebar-root .livewire-menu-sidebar ul.submenu { display: none !important; margin: 0 !important; padding-left: 0 !important; }

        /* show when parent li has `open` */
        .livewire-menu-sidebar-root .livewire-menu-sidebar li.open > ul.submenu { display: block !important; }

        /* small transition */
        .livewire-menu-sidebar-root .livewire-menu-sidebar ul.submenu { transition: max-height 0.14s ease-in-out; overflow: hidden; }

        /* indent submenu links */
        .livewire-menu-sidebar-root .livewire-menu-sidebar ul.submenu .nav-item > a,
        .livewire-menu-sidebar-root .livewire-menu-sidebar ul.submenu .nav-item > .d-flex {
            padding-left: 1rem !important;
        }

        /* make parent toggles clickable */
        .livewire-menu-sidebar-root .livewire-menu-sidebar .dropdown-toggle { cursor: pointer; display: flex; align-items: center; width: 100%; text-decoration: none; }

        /* --- Submenu alignment --- */
        /* Target the submenu UL so it's indented to start at same x-position as parent label text */
        .livewire-menu-sidebar-root .livewire-menu-sidebar ul.submenu {
            padding-left: 0 !important;       /* remove browser default */
            margin-left: 0 !important;
        }

        /* Make each submenu link align with parent's label column */
        .livewire-menu-sidebar-root .livewire-menu-sidebar ul.submenu .nav-item > a,
        .livewire-menu-sidebar-root .livewire-menu-sidebar ul.submenu .nav-item > .d-flex {
            display: flex;
            align-items: center;
            padding: .5rem 1rem;              /* vertical + horizontal padding */
            padding-left: calc(1rem + 1.5rem) !important; /* left padding: base + icon space */
        }

        /* Ensure submenu icons and text line up with parent icon+text */
        .livewire-menu-sidebar-root .livewire-menu-sidebar ul.submenu .menu-icon,
        .livewire-menu-sidebar-root .livewire-menu-sidebar ul.submenu i.fa {
            width: 1.5rem;
            display: inline-block;
            text-align: center;
            margin-right: .5rem;
            font-size: 1.1rem;
        }

        /* Parent label area: force consistent layout so indent calc works */
        .livewire-menu-sidebar-root .livewire-menu-sidebar > flux\\:navlist .nav > .nav-item > a,
        .livewire-menu-sidebar-root .livewire-menu-sidebar .nav > .nav-item > a.dropdown-toggle {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: .5rem 1rem;
        }

        /* Make the label div (text) fill remaining space so alignment is steady */
        .livewire-menu-sidebar-root .livewire-menu-sidebar .flex-1 {
            min-width: 0;
        }

        /* Optionally reduce font-size/spacing for submenu to show hierarchy */
        .livewire-menu-sidebar-root .livewire-menu-sidebar ul.submenu .nav-item .flex-1 {
            font-size: 0.95rem;
            font-weight: 500;
        }

        /* Small visual cue: indent border on submenu (optional) */
        .livewire-menu-sidebar-root .livewire-menu-sidebar ul.submenu {
            border-left: 1px solid rgba(0,0,0,0.03);
            margin-left: .35rem;
            padding-left: .25rem;
        }


    </style>

    <!-- Inline JS (still inside single root element) -->
    <script>
        (function () {
            const root = document.querySelector('.livewire-menu-sidebar-root');
            if (!root) return;
            const container = root.querySelector('.livewire-menu-sidebar');
            if (!container) return;

            // init aria-expanded from server-side 'open' class
            container.querySelectorAll('.dropdown-toggle').forEach(function (el) {
                const li = el.closest('li');
                el.setAttribute('role', 'button');
                el.setAttribute('aria-expanded', (li && li.classList.contains('open')) ? 'true' : 'false');
            });

            // delegate click toggles (works after Livewire updates too)
            container.addEventListener('click', function (ev) {
                const toggle = ev.target.closest('.dropdown-toggle');
                if (!toggle) return;
                ev.preventDefault();

                const li = toggle.closest('li');
                if (!li) return;

                const willOpen = !li.classList.contains('open');

                if (willOpen) {
                    // close siblings at same level (optional)
                    const siblings = Array.from(li.parentElement.children).filter(x => x !== li && x.classList);
                    siblings.forEach(s => s.classList.remove('open'));
                    li.classList.add('open');
                    toggle.setAttribute('aria-expanded', 'true');
                } else {
                    li.classList.remove('open');
                    toggle.setAttribute('aria-expanded', 'false');
                }
            }, false);

            // re-run init after Livewire re-renders this component
            if (window.livewire) {
                window.livewire.hook('message.processed', (message, component) => {
                    // ensure we only target this component's root when re-rendered
                    const scoped = document.querySelector('.livewire-menu-sidebar-root');
                    if (!scoped) return;
                    scoped.querySelectorAll('.dropdown-toggle').forEach(function (el) {
                        const li = el.closest('li');
                        el.setAttribute('role', 'button');
                        el.setAttribute('aria-expanded', (li && li.classList.contains('open')) ? 'true' : 'false');
                    });
                });
            }
        })();
    </script>
</div>
