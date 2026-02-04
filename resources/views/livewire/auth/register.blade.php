<div class="flex flex-col gap-6">

    {{-- DEBUG --}}{{--
    <div class="text-xs text-gray-500">
        mobileChecked: {{ $mobileChecked ? 'YES' : 'NO' }} |
        existingUser: {{ $existingUser ? 'YES' : 'NO' }}
    </div>--}}

    <h2 class="text-lg font-semibold text-center">Register</h2>

    <form wire:submit.prevent="register" class="flex flex-col gap-5">

        {{-- ================= MOBILE ================= --}}
        <input
            type="tel"
            wire:model.live="mobile"
            maxlength="10"
            inputmode="numeric"
            required
            class="form-control w-full rounded-lg border border-gray-300 px-4 py-2.5 uppercase"
            placeholder="ENTER MOBILE NUMBER"

            oninput="
                this.value = this.value.replace(/[^0-9]/g, '');
                this.setCustomValidity('');

                if (this.value.length >= 1 && !/^[6-9]/.test(this.value)) {
                    this.setCustomValidity(
                        'Please enter a valid 10-digit mobile number starting with 6, 7, 8, or 9.'
                    );
                    this.reportValidity();
                    return;
                }

                if (this.value.length === 10 && !/^[6-9][0-9]{9}$/.test(this.value)) {
                    this.setCustomValidity(
                        'Please enter a valid 10-digit mobile number starting with 6, 7, 8, or 9.'
                    );
                    this.reportValidity();
                }
            "

            oninvalid="
                this.setCustomValidity(
                    'Please enter a valid 10-digit mobile number starting with 6, 7, 8, or 9.'
                );
            "
        />

        {{-- ================= STREAM ================= --}}
        <div>
            {{--<label class="block text-sm font-medium text-sky-400 mb-1">
                CHOOSE A STREAM
            </label>--}}

            @if($existingUser)
                <input
                    type="text"
                    readonly
                    class="form-control w-full rounded-lg border px-4 py-2.5 bg-gray-100 cursor-not-allowed uppercase"
                    value="{{ optional($streams->firstWhere('id', $user_stream_id))->mas_stream_name }}"
                />
            @else
                <select
                    wire:model.live="user_stream_id"
                    class="chosen-select form-control text-grey-l1 text-100 w-full rounded-lg border px-4 py-2.5 uppercase"
                >
                    <option value="">CHOOSE A STREAM</option>
                    @foreach ($streams as $stream)
                        <option value="{{ $stream->id }}">
                            {{ strtoupper($stream->mas_stream_name) }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>

        {{-- ================= COLLEGE ================= --}}
        <div>
           {{-- <label class="block text-sm font-medium text-gray-700 mb-1">
                CHOOSE A COLLEGE
            </label>--}}

            @if($existingUser)
                <input
                    type="text"
                    readonly
                    class="form-control w-full rounded-lg border px-4 py-2.5 bg-gray-100 cursor-not-allowed uppercase"
                    value="{{ optional($colleges->firstWhere('id', $user_college_id))->mas_college_name }}"
                />
            @else
                <select
                    wire:model.live="user_college_id"
                    class="chosen-select form-control text-grey-l1 text-100 w-full rounded-lg border px-4 py-2.5 uppercase"
                    @disabled(!$user_stream_id)
                >
                    <option value="">CHOOSE COLLEGE</option>
                    @foreach ($colleges as $college)
                        <option value="{{ $college->id }}">
                            {{ strtoupper($college->mas_college_name) }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>

        {{-- ================= DESIGNATION ================= --}}
        <div>
            {{--<label class="block text-sm font-medium text-gray-700 mb-1">
                CHOOSE A DESIGNATION
            </label>--}}

            @if($existingUser)
                <input
                    type="text"
                    readonly
                    class="form-control w-full rounded-lg border px-4 py-2.5 bg-gray-100 cursor-not-allowed uppercase"
                    value="{{ optional($designations->firstWhere('id', $user_designation_id))->mas_designation_name }}"
                />
            @else
                <select
                    wire:model.live="user_designation_id"
                    class="chosen-select form-control text-grey-l1 text-100 w-full rounded-lg border px-4 py-2.5 uppercase"
                    @disabled(!$user_college_id)
                >
                    <option value="">CHOOSE DESIGNATION</option>
                    @foreach($designations as $designation)
                        <option value="{{ $designation->id }}">
                            {{ strtoupper($designation->mas_designation_name) }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>

        {{-- ================= EMAIL ================= --}}
        <div>
            <label class="block text-sm font-medium text-sky-400 mb-1">
                EMAIL ADDRESS
            </label>

            @if($existingUser)
                <input
                    type="email"
                    readonly
                    class="form-control w-full rounded-lg border px-4 py-2.5 bg-gray-100 cursor-not-allowed uppercase"
                    value="{{ strtoupper($email) }}"
                />
            @else
                <input
                    type="email"
                    wire:model.live.debounce.500ms="email"
                    required
                    placeholder="EMAIL ADDRESS"
                    class="form-control w-full rounded-lg border px-4 py-2.5 uppercase"
                    oninput="
                        this.value = this.value.toUpperCase();
                        this.setCustomValidity('');
                    "
                    oninvalid="
                        this.setCustomValidity('Please enter a valid email address (example@domain.com)');
                    "
                />
            @endif

            @error('email')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- ================= NAME ================= --}}
        <div>
            <label class="block text-sm font-medium text-sky-400 mb-1">
                NAME
            </label>

            @if($existingUser)
                <input
                    type="text"
                    readonly
                    class="form-control w-full rounded-lg border px-4 py-2.5 bg-gray-100 cursor-not-allowed uppercase"
                    value="{{ strtoupper($name) }}"
                />
            @else
                <input
                    type="text"
                    wire:model.live="name"
                    required
                    placeholder="NAME"
                    class="form-control w-full rounded-lg border px-4 py-2.5 uppercase"
                    oninput="
                        this.value = this.value
                            .replace(/[^A-Za-z\\s]/g, '')
                            .toUpperCase();
                    "
                />
            @endif

            @error('name')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- ================= REGISTER BUTTON ================= --}}
        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="register"
            class="w-full rounded-lg bg-gray-400 text-white py-3 font-medium
            hover:bg-gray-500 transition"
            @disabled(!$mobileChecked || $existingUser)
        >
            <span wire:loading.remove wire:target="register">
                REGISTER
            </span>

            <span wire:loading wire:target="register">
                <svg class="animate-spin h-5 w-5 text-white"
                     xmlns="http://www.w3.org/2000/svg"
                     fill="none"
                     viewBox="0 0 24 24">
                    <circle class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"/>
                    <path class="opacity-75"
                          fill="currentColor"
                          d="M4 12a8 8 0 018-8v8z"/>
                </svg>
            </span>
        </button>

    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-sky-400 dark:text-zinc-400">
        <span>{{ __('Already registered ? ') }}</span>
        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
</div>
