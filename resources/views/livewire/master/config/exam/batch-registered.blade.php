@if($show)
    <div class="fixed inset-0 z-50 flex justify-center items-start overflow-auto">

        {{-- Blur + disable background --}}
        <div class="absolute inset-0 backdrop-blur-sm bg-white/40"></div>

        {{-- Modal --}}
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-6xl mx-2 sm:mx-6 my-6 flex flex-col max-h-[95vh] border">

            {{-- HEADER --}}
            <div class="flex justify-between items-center border-b px-4 py-3 bg-gray-50">
                <h2 class="text-lg sm:text-xl font-semibold">
                    Upload Eligible Student Details
                </h2>
                <button type="button" wire:click="close" class="text-2xl text-gray-600 hover:text-black">
                    ×
                </button>
            </div>

            {{-- BODY --}}
            <div
                x-data="{
                search: '',
                rows: @js($validatedRows ?? []),

                failed() {
                    return this.rows.filter(r => r.errors?.length)
                },

                success() {
                    return this.rows.filter(r => !r.errors?.length)
                },

                filtered() {
                    const all = this.failed()

                    if (!this.search) return all

                    const s = this.search.toLowerCase()

                    return all.filter(r =>
                        JSON.stringify(r.data).toLowerCase().includes(s) ||
                        r.errors.join(' ').toLowerCase().includes(s) ||
                        String(r.row_num).includes(s)
                    )
                },

                hasError(row, keyword) {
                    return row.errors.some(e => e.toLowerCase().includes(keyword))
                }
            }"
                class="flex-1 overflow-hidden flex flex-col"
            >

                {{-- SUMMARY BAR --}}
                <div class="px-4 py-3 bg-red-50 border-b text-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div class="space-x-4 font-medium">
                        <span class="text-red-600">❌ <span x-text="failed().length"></span> Failed</span>
                        <span class="text-green-600">✅ <span x-text="success().length"></span> Valid</span>
                        <span class="text-gray-600">📦 <span x-text="rows.length"></span> Total</span>
                    </div>

                    <input
                        type="text"
                        x-model="search"
                        placeholder="Search subject, centre, row..."
                        class="border px-3 py-2 rounded text-sm w-full sm:w-72"
                    >
                </div>

                {{-- TABLE --}}
                <div class="overflow-auto flex-1">
                    <table class="w-full min-w-[900px] border-collapse text-sm">
                        <thead class="bg-gray-100 sticky top-0 z-10">
                        <tr>
                            <th class="border px-3 py-2 w-20">Row #</th>
                            <th class="border px-3 py-2">Details</th>
                            <th class="border px-3 py-2 w-[360px]">Errors</th>
                        </tr>
                        </thead>

                        <tbody>
                        <template x-for="row in filtered()" :key="row.row_num">
                            <tr class="hover:bg-gray-50 align-top">

                                {{-- ROW --}}
                                <td class="border px-3 py-2 text-center font-semibold">
                                    <span x-text="row.row_num"></span>
                                </td>

                                {{-- DETAILS --}}
                                <td class="border px-3 py-2">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-1 text-sm">

                                        <div :class="hasError(row,'faculty') ? 'text-red-600 font-semibold' : ''">
                                            Faculty: <span x-text="row.data.facultyname ?? '-'"></span>
                                        </div>

                                        <div :class="hasError(row,'course') ? 'text-red-600 font-semibold' : ''">
                                            Course Level: <span x-text="row.data.courselevel ?? '-'"></span>
                                        </div>

                                        <div :class="hasError(row,'subject') ? 'text-red-600 font-semibold' : ''">
                                            Subject: <span x-text="row.data.subject ?? '-'"></span>
                                        </div>

                                        <div :class="hasError(row,'scheme') ? 'text-red-600 font-semibold' : ''">
                                            Scheme: <span x-text="row.data.scheme ?? '-'"></span>
                                        </div>

                                        <div :class="hasError(row,'centre') ? 'text-red-600 font-semibold' : ''">
                                            Centre Code: <span x-text="row.data.centrecode ?? '-'"></span>
                                        </div>

                                        <div :class="hasError(row,'attached') ? 'text-red-600 font-semibold' : ''">
                                            Attached College: <span x-text="row.data.attachedcollege ?? '-'"></span>
                                        </div>

                                        <div :class="hasError(row,'year') ? 'text-red-600 font-semibold' : ''">
                                            Exam Year: <span x-text="row.data.examyear ?? '-'"></span>
                                        </div>

                                        <div :class="hasError(row,'month') ? 'text-red-600 font-semibold' : ''">
                                            Exam Month: <span x-text="row.data.exammonth ?? '-'"></span>
                                        </div>

                                        <div>
                                            Student Count: <span x-text="row.data.studentcount ?? '-'"></span>
                                        </div>

                                        <div :class="hasError(row,'date') ? 'text-red-600 font-semibold' : ''">
                                            Start Date: <span x-text="row.data.examstartdate ?? '-'"></span>
                                        </div>

                                    </div>
                                </td>

                                {{-- ERRORS --}}
                                <td class="border px-3 py-2 text-red-600">
                                    <template x-for="err in row.errors" :key="err">
                                        <div class="mb-1">• <span x-text="err"></span></div>
                                    </template>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="filtered().length === 0">
                            <td colspan="3" class="text-center py-6 text-gray-500">
                                No matching errors found.
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="border-t px-4 py-3 flex justify-end bg-gray-50">
                <button class="btn btn-secondary px-6" wire:click="close">
                    Close
                </button>
            </div>

        </div>
    </div>
@endif
