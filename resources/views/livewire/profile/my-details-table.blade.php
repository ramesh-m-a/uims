<div class="space-y-4">

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
     | PAGE TITLE ROW
     ========================= --}}
    <div class="flex justify-between items-center">
        <h1 class="text-1xl leading-none text-sky-700 font-extrabold">
            My Details
        </h1>
    </div>

    {{-- =========================
     | CONTENT CARD
     ========================= --}}
    <div class="bg-white border rounded-lg p-4">

        <table class="w-full text-sm border-collapse">
            <thead class="bg-gray-100">
            <tr>
                <th class="border px-3 py-2">Photo</th>
                <th class="border px-3 py-2">Name</th>
                <th class="border px-3 py-2">Mobile</th>
                <th class="border px-3 py-2">Email</th>
                <th class="border px-3 py-2">Designation</th>
                <th class="border px-3 py-2">Department</th>
                <th class="border px-3 py-2">College</th>
                <th class="border px-3 py-2">TIN</th>
                <th class="border px-3 py-2">Status</th>
                <th class="border px-3 py-2 text-center">Action</th>
            </tr>
            </thead>

            <tbody>
            <tr>
                @php
                    use Illuminate\Support\Facades\Storage;

                    $photoUrl = null;

                    if (!empty($user->photo_path)) {

                        // Normalize path (remove double slashes, trim spaces)
                        $cleanPath = trim(str_replace('//', '/', $user->photo_path));

                        // Remove extension safely
                        $base = pathinfo($cleanPath, PATHINFO_DIRNAME) . '/' . pathinfo($cleanPath, PATHINFO_FILENAME);

                        foreach (['webp', 'jpg', 'jpeg', 'png'] as $ext) {
                            $path = "{$base}.{$ext}";

                            if (Storage::disk('public')->exists($path)) {
                                $photoUrl = Storage::disk('public')->url($path)
                                    . '?v=' . Storage::disk('public')->lastModified($path);
                                break;
                            }
                        }
                    }

                    if (!$photoUrl) {
                        $photoUrl = 'https://ui-avatars.com/api/?name=' . urlencode($user->name);
                    }
                @endphp
                {{-- PHOTO --}}
                <td class="border px-2 py-2 text-center">
                    <img
                        src="{{ $photoUrl }}"
                        class="h-8 w-8 rounded-full object-cover mx-auto cursor-pointer
           hover:ring-2 hover:ring-blue-500"
                        alt="Profile Photo"
                        wire:click="$set('showPhotoModal', true)"
                    >
                </td>

                {{-- NAME --}}
                <td class="border px-2 py-2">
                    {{ strtoupper($user->name) }}
                </td>

                {{-- MOBILE --}}
                <td class="border px-2 py-2">
                    {{ $mobile ?: '-' }}
                </td>

                {{-- EMAIL --}}
                <td class="border px-2 py-2">
                    {{ strtoupper($user->email) }}
                </td>

                {{-- DESIGNATION --}}
                <td class="border px-2 py-2">
                    {{ $designationName ? strtoupper($designationName) : '-' }}
                </td>

                {{-- DEPARTMENT --}}
                <td class="border px-2 py-2">
                    {{ $departmentName ? strtoupper($departmentName) : '-' }}
                </td>

                {{-- COLLEGE --}}
                <td class="border px-2 py-2">
                    {{ $collegeName ? strtoupper($collegeName) : '-' }}
                </td>

                {{-- TIN --}}
                <td class="border px-2 py-2">
                    {{ strtoupper($user->user_tin)  ?: '-' }}
                </td>

                {{-- STATUS --}}
                <td class="border px-2 py-2 text-center">
                    @php
                        $statusCode = $draft?->status_id ?? 'draft';

                        $statusColor = match ($statusCode) {
                            'submitted' => 'bg-blue-600',
                            'approved'  => 'bg-green-600',
                            'rejected'  => 'bg-red-600',
                            default     => 'bg-gray-400',
                        };

                        $statusName = $draft?->statusMaster?->mas_status_name ?? 'DRAFT';
                        $statusDesc = $draft?->statusMaster?->mas_status_description ?? 'Draft not yet submitted';
                    @endphp

                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full
                               text-xs font-semibold text-white whitespace-nowrap
                               {{ $statusColor }}"
                        title="{{ $statusDesc }}"
                    >
                        {{ strtoupper($statusName) }}
                    </span>
                </td>

                {{-- ACTION --}}
                <td class="border px-2 py-2 text-center space-x-3">
                    @php
                        $canEdit = is_null($draft) || $draft->status_id === 'draft';
                    @endphp

                    @if($canEdit)
                        <a href="{{ route('profile.edit.wizard') }}"
                           class="text-gray-700 hover:text-black"
                           title="Edit">
                            <i class="fa fa-pencil-alt"></i>
                        </a>
                    @endif

                    <a href="#"
                       class="text-black hover:text-blue-600"
                       title="View">
                        <i class="fa fa-eye"></i>
                    </a>
                </td>

            </tr>
            </tbody>
        </table>

        {{-- 🔑 REUSED PHOTO PREVIEW MODAL --}}
        @include('components.photo-preview-modal')

    </div>

    @include('livewire.profile._audit-history')
</div>
