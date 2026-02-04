<div class="bg-white border rounded-xl shadow-sm">
    <div class="px-6 py-4 border-b flex justify-between items-center">
        <h2 class="font-semibold text-gray-800 text-lg">Documents</h2>
        <span class="text-sm text-gray-500">Upload Clear PDF/JPG/PNG files</span>
    </div>

    {{-- ERROR --}}
    @error('documents')
    <div class="bg-red-100 text-red-700 px-4 py-2 rounded">
        {{ $message }}
    </div>
    @enderror

    <div class="p-6 space-y-5">
    @foreach ($requiredDocuments as $docId => $docLabel)
            @php
                $uploaded = isset($documents[$docId]);
                $filePath = $documents[$docId]['file_path'] ?? null;
                $fileName = $documents[$docId]['original_name']
                            ?? basename($filePath ?? '');
            @endphp

            <div class="border rounded-xl p-5 hover:shadow-md transition bg-gray-50">

                {{-- Header --}}
                <div class="flex justify-between items-center mb-3">
                    <div>
                        <h3 class="font-medium text-gray-800">
                            {{ $docLabel }}
                            <span class="text-red-500">*</span>
                        </h3>

                        @if($uploaded)
                            <p class="text-sm text-gray-600 mt-1">
                                📄 {{ $fileName }}
                            </p>
                        @else
                            <p class="text-sm text-gray-400 mt-1">
                                No file uploaded
                            </p>
                        @endif
                    </div>

                    {{-- Status --}}
                    <div>
                        @if($uploaded)
                            <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700 font-medium">
                                ✔ Uploaded
                            </span>
                        @else
                            <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700 font-medium">
                                Pending
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-wrap gap-3 items-center">

                    {{-- View --}}
                    @if($uploaded)
                        <button
                            type="button"
                            wire:ignore
                            onclick="openDocumentPreview('{{ asset('storage/'.$documents[$docId]['file_path']) }}')"
                            class="text-blue-600 hover:text-blue-800 font-medium">
                            👁 View
                        </button>

                        {{-- Delete --}}
                        <button
                            type="button"
                            wire:click="deleteDocument({{ $docId }})"
                            onclick="return confirm('Delete this document?')"
                            class="px-4 py-2 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200">
                            🗑 Delete
                        </button>
                    @endif

                    {{-- Upload --}}
                    <div class="flex items-center gap-3 ml-auto">

                        <input
                            type="file"
                            wire:model="documentFile"
                            class="text-sm file:border file:rounded file:px-3 file:py-1
                                   file:bg-gray-100 file:text-gray-700 file:cursor-pointer"
                        >

                        <button
                            type="button"
                            wire:click="uploadDocument({{ $docId }})"
                            @disabled($activeDocumentId !== (int) $docId)
                            class="px-5 py-2 text-sm rounded font-medium
                            {{ $activeDocumentId === (int) $docId
                                ? 'bg-blue-600 text-white hover:bg-blue-700'
                                : 'bg-gray-200 text-gray-500 cursor-not-allowed' }}">
                            ⬆ Upload
                        </button>

                    </div>
                </div>

                @error('documentFile')
                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror

            </div>
    @endforeach
    </div>
    {{-- ACTION BAR --}}
    <div class="flex justify-end pt-6">
        <button
            wire:click="save"
            class="px-6 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700"
        >
            Save & Continue →
        </button>
    </div>

    {{-- ================= DOCUMENT PREVIEW MODAL ================= --}}
    @if($previewUrl)
        <div class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center">

            <div class="bg-white w-full h-full md:w-[90%] md:h-[90%] rounded-lg shadow-xl relative overflow-hidden">

                {{-- HEADER --}}
                <div class="flex justify-between items-center px-4 py-3 border-b bg-gray-50">
                    <h2 class="text-sm font-semibold text-gray-700">
                        Document Preview
                    </h2>

                    <button
                        wire:click="$set('previewUrl', null)"
                        class="text-gray-500 hover:text-black text-2xl leading-none"
                    >
                        &times;
                    </button>
                </div>

                {{-- CONTENT --}}
                <div class="w-full h-[calc(100%-52px)] bg-gray-100">
                    <iframe
                        src="{{ $previewUrl }}"
                        class="w-full h-full border-0"
                    ></iframe>
                </div>

            </div>
        </div>
    @endif

    {{-- ================= DOCUMENT PREVIEW MODAL ================= --}}
    <div id="documentPreviewModal"
         class="fixed inset-0 hidden z-50">

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-black/60"></div>

        {{-- Centered container --}}
        <div class="relative w-full h-full flex items-center justify-center p-4">

            <div class="bg-white w-full max-w-6xl h-[90vh] rounded-2xl shadow-2xl flex flex-col overflow-hidden">

                {{-- Header --}}
                <div class="flex justify-between items-center px-6 py-4 border-b bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-800">Document Preview</h3>

                    <button onclick="closeDocumentPreview()"
                            class="text-gray-500 hover:text-black text-2xl leading-none">
                        ✕
                    </button>
                </div>

                {{-- Body --}}
                <div class="flex-1 bg-gray-100 flex items-center justify-center">

                    <iframe
                        id="documentPreviewFrame"
                        class="w-full h-full border-0 bg-white rounded-b-2xl"
                        src="">
                    </iframe>

                </div>

            </div>
        </div>
    </div>

    <script>
        function openDocumentPreview(url) {
            const modal = document.getElementById('documentPreviewModal');
            const frame = document.getElementById('documentPreviewFrame');

            frame.src = url;
            modal.classList.remove('hidden');

            document.body.classList.add('overflow-hidden'); // lock background scroll
        }

        function closeDocumentPreview() {
            const modal = document.getElementById('documentPreviewModal');
            const frame = document.getElementById('documentPreviewFrame');

            frame.src = '';
            modal.classList.add('hidden');

            document.body.classList.remove('overflow-hidden');
        }
    </script>
</div>
