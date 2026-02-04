<?php

use App\Models\Admin\User;
use App\Models\Master\Config\Academic\College;
use App\Models\Master\Config\Academic\Designation;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public string $mobile = '';
    public $photo;

    public ?int $college_id = null;
    public ?int $designation_id = null;

    public array $colleges = [];
    public array $designations = [];

    /* ================= MOUNT ================= */

    public function mount(): void
    {
        $user = Auth::user();

        $this->name = strtoupper($user->name);
        $this->email = $user->email;
        $this->mobile = $user->mobile ?? '';

        $this->college_id = $user->user_college_id;
        $this->designation_id = $user->user_designation_id;

        // Colleges restricted to same stream (UPPERCASE for display)
        $this->colleges = College::query()
            ->where('mas_college_stream_id', $user->user_stream_id)
            ->where('mas_college_status_id', 1)
            ->orderBy('mas_college_name')
            ->get(['id', 'mas_college_name','mas_college_code'])
            ->map(fn ($c) => [
                'id' => $c->id,
                'mas_college_name' => strtoupper($c->mas_college_name),
                'mas_college_code' => strtoupper($c->mas_college_code),
            ])
            ->toArray();

        // Active designations (UPPERCASE for display)
        $this->designations = Designation::query()
            ->where('mas_designation_status_id', 1)
            ->orderBy('mas_designation_name')
            ->get(['id', 'mas_designation_name'])
            ->map(fn ($d) => [
                'id' => $d->id,
                'mas_designation_name' => strtoupper($d->mas_designation_name),
            ])
            ->toArray();
    }

    /* ================= PHOTO UPLOAD ================= */

    public function updatedPhoto(): void
    {

       /* $this->validate([
            'photo' => ['required', 'mimetypes:jpg,jpeg,png,image/webp', 'max:2048'],
        ]);*/

        $this->validate([
            'photo' => [
                'required',
                'image',
                'max:1024',
            ],
        ]);

        $user = Auth::user();
        $oldPath = $user->photo_path;

        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        $path = $this->photo->store('profile-photos', 'public');

        $user->update(['photo_path' => $path]);

        AuditLogger::log(
            table: 'users',
            recordId: $user->id,
            action: 'update',
            oldValues: ['photo_path' => $oldPath],
            newValues: ['photo_path' => $path],
        );

        $this->reset('photo');
        $user->refresh();
        $this->dispatch('$refresh');
        $this->dispatch('profile-photo-updated');
    }

    /* ================= PROFILE UPDATE ================= */

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:15'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'college_id' => [
                'required',
                Rule::exists('mas_college', 'id')->where('mas_college_stream_id', $user->user_stream_id),
            ],
            'designation_id' => [
                'required',
                'exists:mas_designation,id',
            ],
        ]);

        // Store name in uppercase consistently
        $validated['name'] = strtoupper($validated['name']);

        $original = $user->only([
            'name',
            'email',
            'mobile',
            'college_id',
            'designation_id',
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        AuditLogger::log(
            table: 'users',
            recordId: $user->id,
            action: 'update',
            oldValues: $original,
            newValues: $validated,
        );

        $this->dispatch('profile-updated', name: $user->name);
    }

    /* ================= EMAIL ================= */

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();
        Session::flash('Status', 'verification-link-sent');
    }
};
?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout
        :heading="__('Profile')"
        :subheading="__('Manage your profile information')"
    >

        {{-- ================= PROFILE PHOTO ================= --}}
        <div class="flex flex-col items-center mb-10">
            <label for="photoUpload" class="cursor-pointer">
                @php
                    $user = auth()->user();
                    $photoUrl = null;

                    if ($user->photo_path) {
                        $path = 'storage/' . $user->photo_path;

                        if (file_exists(public_path($path))) {
                            $photoUrl = asset($path);
                        } else {
                            $base = preg_replace('/\.(jpg|jpeg|png|webp)$/i', '', $user->photo_path);
                            foreach (['webp', 'jpg', 'jpeg', 'png'] as $ext) {
                                $try = "storage/{$base}.{$ext}";
                                if (file_exists(public_path($try))) {
                                    $photoUrl = asset($try);
                                    break;
                                }
                            }
                        }
                    }
                @endphp

                <img
                    src="{{ $photoUrl ? $photoUrl . '?v=' . time() : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}"
                    class="h-37 w-32 rounded-full object-cover border"
                    alt="Profile Photo"
                />
            </label>

           {{-- <input id="photoUpload" type="file" class="hidden" wire:model="photo" accept="image/png,image/jpeg"/>--}}

            <input
                id="photoUpload"
                type="file"
                class="hidden"
                wire:model="photo"
                accept="image/*"
            />

            <flux:text class="mt-2 text-sm text-gray-500">
                Click photo to change (JPG / PNG, max 2MB)
            </flux:text>
        </div>

        {{-- ================= READ ONLY ================= --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <flux:input
                label="Stream"
                disabled
                value="{{ strtoupper(auth()->user()->stream->mas_stream_name ?? '') }}"
            />
        </div>

        {{-- ================= EDITABLE ================= --}}
        <form wire:submit="updateProfileInformation" class="space-y-6">

            <flux:select wire:model="college_id" label="College" required>
                <option value="">SELECT COLLEGE</option>
                @foreach($colleges as $c)
                    <option value="{{ $c['id'] }}">{{ $c['mas_college_code'] }}-{{ $c['mas_college_name'] }}</option>
                @endforeach
            </flux:select>

            <flux:select wire:model="designation_id" label="Designation" required>
                <option value="">SELECT DESIGNATION</option>
                @foreach($designations as $d)
                    <option value="{{ $d['id'] }}">{{ $d['mas_designation_name'] }}</option>
                @endforeach
            </flux:select>

            <flux:input
                wire:model="name"
                label="Name"
                required
                class="uppercase"
            />

            <flux:input wire:model="email" label="Email" type="email" required/>
            <flux:input wire:model="mobile" label="Mobile Number"/>

            @if (
                auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&
                ! auth()->user()->hasVerifiedEmail()
            )
                <flux:text class="text-sm">
                    Email not verified.
                    <flux:link wire:click.prevent="resendVerificationNotification">
                        Resend verification
                    </flux:link>
                </flux:text>
            @endif

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">
                    Save
                </flux:button>

                <x-action-message on="profile-updated">
                    Saved.
                </x-action-message>
            </div>
        </form>

        <livewire:settings.delete-user-form/>
    </x-settings.layout>
</section>
