<div>
    @if($show)
        <div class="fixed inset-0 z-50 flex justify-center items-start bg-black/20 backdrop-blur-sm overflow-auto">

            <div class="bg-white w-full max-w-6xl m-4 rounded-xl shadow-xl border flex flex-col max-h-[95vh]">

                {{-- HEADER --}}
                <div class="flex justify-between items-center px-5 py-4 border-b bg-gray-50">
                    <h2 class="text-lg font-semibold">Upload Eligible Student Details</h2>
                    <button wire:click="close" class="text-2xl text-gray-500 hover:text-black">×</button>
                </div>

                {{-- BODY --}}
                <div
                    class="flex-1 overflow-auto"
                    x-data="{
                        search: '',
                        get rows() { return @js($validatedRows) },

                        failed() { return this.rows.filter(r => r.errors.length) },
                        passed() { return this.rows.filter(r => !r.errors.length) },

                        filtered() {
                            if (!this.search) return this.failed()
                            const s = this.search.toLowerCase()
                            return this.failed().filter(r =>
                                JSON.stringify(r.data).toLowerCase().includes(s) ||
                                r.errors.join(' ').toLowerCase().includes(s) ||
                                String(r.row_num).includes(s)
                            )
                        }
                    }"
                >

                    {{-- UPLOAD --}}
                    @if(!$hasErrors)
                        <div class="p-6 space-y-4">

                            <input type="file" wire:model="file" class="w-full border border-gray-300 rounded-lg p-2 text-sm">

                            @error('file')
                            <div class="text-red-600 text-sm">{{ $message }}</div>
                            @enderror

                            <div class="flex justify-end gap-3">
                                <button
                                    wire:click="close"
                                    class="px-4 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 hover:border-gray-400 transition"
                                >
                                    Cancel
                                </button>

                                <button
                                    wire:click="processUpload"
                                    class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition"
                                >
                                    Upload
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- ERRORS --}}
                    @if($hasErrors)
                        <div class="border-t">

                            {{-- SUMMARY --}}
                            {{-- STATUS BANNER --}}
                            <div class="px-5 py-4 bg-amber-50 border-b border-amber-200 flex justify-between items-start">

                                <div>
                                    <div class="font-semibold text-amber-800 text-sm">
                                        ⚠ Upload completed with errors
                                    </div>

                                    <div class="text-sm text-amber-700 mt-1">
                                        <b x-text="failed().length"></b> row(s) need attention before continuing.
                                    </div>

                                    <div class="text-sm text-green-700 mt-1">
                                        ✔ <b x-text="passed().length"></b> rows ready to import
                                    </div>

                                    <div class="text-sm text-sky-500 mt-1">
                                        Total Rows: <b x-text="rows.length"></b>
                                    </div>
                                </div>

                                <div>
                                    <input
                                        x-model="search"
                                        placeholder="Search row, subject, centre..."
                                        class="border border-gray-300 px-3 py-2 rounded-lg w-72 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white"
                                    >
                                </div>

                            </div>

                            {{-- TABLE --}}
                            <div class="overflow-auto max-h-[65vh]">
                                <table class="w-full text-sm border-collapse">
                                    <thead class="bg-gray-100 sticky top-0">
                                    <tr>
                                        <th class="border px-3 py-2 w-20">Row</th>
                                        <th class="border px-3 py-2">Details</th>
                                        <th class="border px-3 py-2 w-[360px]">Errors</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <template x-for="row in filtered()" :key="row.row_num">
                                        <tr class="align-top hover:bg-gray-50">
                                            <td class="border px-3 py-2 text-center font-semibold" x-text="row.row_num"></td>

                                            <td class="border px-3 py-2">
                                                <div class="grid grid-cols-2 gap-2 text-sm">
                                                    <div><b>Faculty:</b> <span x-text="row.data.facultyname"></span></div>
                                                    <div><b>Course:</b> <span x-text="row.data.courselevel"></span></div>
                                                    <div><b>Subject:</b> <span x-text="row.data.subject"></span></div>
                                                    <div><b>Scheme:</b> <span x-text="row.data.scheme"></span></div>
                                                    <div><b>Centre:</b> <span x-text="row.data.centrecode"></span></div>
                                                    <div><b>Month:</b> <span x-text="row.data.exammonth"></span></div>
                                                </div>
                                            </td>

                                            <td class="border px-3 py-2 text-red-600">
                                                <template x-for="err in row.errors" :key="err">
                                                    <div>• <span x-text="err"></span></div>
                                                </template>
                                            </td>
                                        </tr>
                                    </template>
                                    </tbody>
                                </table>
                            </div>

                            {{-- FOOTER --}}
                            <div class="p-4 flex justify-end border-t bg-gray-50">
                                <button
                                    wire:click="close"
                                    class="px-4 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 hover:border-gray-400 transition"
                                >
                                    Close
                                </button>
                            </div>

                        </div>
                    @endif

                </div>
            </div>
        </div>
    @endif
</div>
