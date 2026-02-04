@php
    $mask = fn ($v, $show = 4) =>
        $v ? str_repeat('*', max(strlen($v) - $show, 0)) . substr($v, -$show) : '-';
@endphp

<div class="space-y-6">

    {{-- ================= BASIC DETAILS ================= --}}
    <div class="basic-tab ace-scope bg-gray-50 border rounded-lg p-6 space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium mb-1">Date of Birth</label>
                <input type="text" readonly
                       value="{{ data_get($draft->data, 'basic.dob', '-') }}"
                       class="form-input w-full bg-gray-100">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Gender</label>
                <input type="text" readonly
                       value="{{ strtoupper($genderName ?? '-') }}"
                       class="form-input w-full bg-gray-100">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Age</label>
                <input type="text" readonly
                       value="{{ $calculatedAge ?? '-' }}"
                       class="form-input w-full bg-gray-100">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium mb-1">Father / Spouse Name</label>
                <input type="text" readonly
                       value="{{ strtoupper(data_get($draft->data,'basic.father_name','-')) }}"
                       class="form-input w-full bg-gray-100">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Religion</label>
                <input type="text" readonly
                       value="{{ strtoupper(data_get($draft->data,'basic.religion_name','-')) }}"
                       class="form-input w-full bg-gray-100">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Category</label>
                <input type="text" readonly
                       value="{{ strtoupper(data_get($draft->data,'basic.category_name','-')) }}"
                       class="form-input w-full bg-gray-100">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium mb-1">Designation</label>
                <input type="text" readonly
                       value="{{ strtoupper(data_get($draft->data,'basic.designation_name','-')) }}"
                       class="form-input w-full bg-gray-100">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Department</label>
                <input type="text" readonly
                       value="{{ strtoupper(data_get($draft->data,'basic.department_name','-')) }}"
                       class="form-input w-full bg-gray-100">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Administrative Role</label>
                <input type="text" readonly
                       value="{{ strtoupper(data_get($draft->data,'basic.admin_role_name','-')) }}"
                       class="form-input w-full bg-gray-100">
            </div>
        </div>
    </div>

    {{-- ================= ADDRESS ================= --}}
    <div class="basic-tab ace-scope bg-gray-50 border rounded-lg p-6 space-y-6">

        <h3 class="text-blue-700 font-semibold">Permanent Address</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <input readonly class="form-input bg-gray-100" value="{{ data_get($draft->data,'address.permanent.address_1','-') }}">
            <input readonly class="form-input bg-gray-100" value="{{ data_get($draft->data,'address.permanent.address_2','-') }}">
            <input readonly class="form-input bg-gray-100" value="{{ data_get($draft->data,'address.permanent.address_3','-') }}">
            <input readonly class="form-input bg-gray-100" value="{{ data_get($draft->data,'address.permanent.district','-') }}">
            <input readonly class="form-input bg-gray-100" value="{{ data_get($draft->data,'address.permanent.state_name','-') }}">
            <input readonly class="form-input bg-gray-100" value="{{ data_get($draft->data,'address.permanent.pincode','-') }}">
        </div>

        <h3 class="text-blue-700 font-semibold">Correspondence Address</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <input readonly class="form-input bg-gray-100" value="{{ data_get($draft->data,'address.temporary.address_1','-') }}">
            <input readonly class="form-input bg-gray-100" value="{{ data_get($draft->data,'address.temporary.address_2','-') }}">
            <input readonly class="form-input bg-gray-100" value="{{ data_get($draft->data,'address.temporary.address_3','-') }}">
            <input readonly class="form-input bg-gray-100" value="{{ data_get($draft->data,'address.temporary.district','-') }}">
            <input readonly class="form-input bg-gray-100" value="{{ data_get($draft->data,'address.temporary.state_name','-') }}">
            <input readonly class="form-input bg-gray-100" value="{{ data_get($draft->data,'address.temporary.pincode','-') }}">
        </div>
    </div>

    {{-- ================= QUALIFICATION ================= --}}
    <div class="bg-white border rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b">
            <h2 class="font-semibold text-gray-800">Qualification Details</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">Degree</th>
                    <th class="px-4 py-3 text-left">Specialisation</th>
                    <th class="px-4 py-3 text-left">Institution</th>
                    <th class="px-4 py-3 text-center">Year Exam</th>
                    <th class="px-4 py-3 text-center">Year Award</th>
                    <th class="px-4 py-3 text-left">Reg No</th>
                </tr>
                </thead>
                <tbody class="divide-y">
                @foreach($qualificationView as $q)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $q['degree'] }}</td>
                        <td class="px-4 py-3">{{ $q['specialisation'] }}</td>
                        <td class="px-4 py-3">{{ $q['institution'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $q['year_exam'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $q['year_award'] }}</td>
                        <td class="px-4 py-3">{{ $q['reg_no'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= WORK ================= --}}
    <div class="border rounded-lg p-4">
        <h3 class="font-semibold mb-4">Work Experience</h3>
        <table class="w-full text-sm border">
            <thead class="bg-gray-100">
            <tr><th>Designation</th><th>Institution</th><th>From</th><th>To</th></tr>
            </thead>
            <tbody>
            @foreach($workView as $w)
                <tr>
                    <td>{{ strtoupper($w['designation']) }}</td>
                    <td>{{ strtoupper($w['institution']) }}</td>
                    <td>{{ $w['from'] }}</td>
                    <td>{{ $w['to'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- ================= BANK ================= --}}
    <div class="basic-tab ace-scope bg-gray-50 border rounded-lg p-6 space-y-4">
        <input readonly class="form-input bg-gray-100" value="PAN : {{ $mask(data_get($draft->data,'bank.identity.pan_number')) }}">
        <input readonly class="form-input bg-gray-100" value="Account : {{ $mask(data_get($draft->data,'bank.account.account_number')) }}">
        <input readonly class="form-input bg-gray-100" value="IFSC : {{ strtoupper(data_get($draft->data,'bank.account.ifs_code','-')) }}">
        <input readonly class="form-input bg-gray-100" value="Bank : {{ strtoupper(data_get($draft->data,'bank.account.bank_name','-')) }}">
        <input readonly class="form-input bg-gray-100" value="Branch : {{ strtoupper(data_get($draft->data,'bank.account.branch_name','-')) }}">
    </div>

    {{-- ================= DOCUMENTS ================= --}}
    <div class="bg-white border rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b">
            <h2 class="font-semibold text-gray-800">Documents</h2>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="px-4 py-3 text-left">Document</th>
                <th class="px-4 py-3 text-center w-32">Action</th>
            </tr>
            </thead>
            <tbody class="divide-y">
            @forelse(data_get($draft->data,'documents',[]) as $doc)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $doc['document_name'] ?? 'Document' }}</td>
                    <td class="px-4 py-3 text-center">
                        <button
                            onclick="openDocumentPreview('{{ asset('storage/'.$doc['file_path']) }}')"
                            class="text-blue-600 hover:text-blue-800 font-medium">
                            👁 View
                        </button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="2">No documents uploaded</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- ================= TERMS & CONDITIONS ================= --}}
    {{-- ================= PREMIUM TERMS MODAL ================= --}}
    <div id="termsModal"
         class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">

        <div class="bg-white w-11/12 md:w-3/5 max-w-3xl rounded-xl shadow-xl flex flex-col overflow-hidden">

            {{-- Header --}}
            <div class="flex justify-between items-center px-6 py-4 border-b">
                <h2 class="text-lg font-semibold">Terms & Conditions</h2>
                <button onclick="closeTermsModal()" class="text-gray-500 hover:text-black text-xl">✕</button>
            </div>

            {{-- Body --}}
            <div class="p-6 space-y-4">

                <div id="termsScrollBox"
                     class="h-64 overflow-y-auto border rounded-lg p-4 text-sm leading-relaxed bg-gray-50">

                    <p class="mb-2">By submitting this registration form, I hereby declare that:</p>

                    <ol class="list-decimal ml-6 space-y-2">
                        <li>The information provided is true and complete to the best of my knowledge.</li>
                        <li>False or misleading information may result in rejection or cancellation.</li>
                        <li>I agree to abide by RGUHS rules and policies.</li>
                        <li>I consent to the use of my personal data for official purposes.</li>
                        <li>RGUHS may request additional verification.</li>
                        <li>Submission does not guarantee approval.</li>
                        <li>I have read and understood the entire registration process.</li>
                    </ol>

                    <p class="mt-4 font-medium">
                        Electronic Signature:
                        By submitting, I provide my electronic consent.
                    </p>
                </div>

                <div class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" id="termsCheckbox" disabled>
                    <label for="termsCheckbox">I have read and agree to the Terms & Conditions</label>
                </div>

            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50">

                <button onclick="closeTermsModal()"
                        class="px-4 py-2 rounded bg-gray-200">
                    Cancel
                </button>

                <button
                    id="finalSubmitBtn"
                    disabled
                    wire:click="submit"
                    class="px-6 py-2 rounded bg-blue-600 text-white disabled:opacity-40 disabled:cursor-not-allowed">
                    Accept & Submit for Verification
                </button>

            </div>
        </div>
    </div>


    {{-- ================= ACTIONS ================= --}}
    {{-- ================= ACTIONS ================= --}}
    <div class="flex justify-between pt-4">
        <button
            wire:click="$dispatch('goToTab', 'documents')"
            class="px-4 py-2 bg-gray-200 rounded">
            ← Back
        </button>

        {{-- Opens modal instead of submitting directly --}}
        <button
            onclick="openTermsModal()"
            class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Save & Continue →
        </button>
    </div>


    {{-- MODAL --}}
    <div id="documentPreviewModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50">
        <div class="bg-white w-11/12 md:w-4/5 h-5/6 rounded-lg flex flex-col">
            <div class="flex justify-between px-4 py-2 border-b">
                <span>Document Preview</span>
                <button onclick="closeDocumentPreview()">✕</button>
            </div>
            <iframe id="documentPreviewFrame" class="flex-1 w-full border"></iframe>
        </div>
    </div>

    <script>
        function openDocumentPreview(url) {
            document.getElementById('documentPreviewFrame').src = url;
            document.getElementById('documentPreviewModal').classList.remove('hidden');
        }
        function closeDocumentPreview() {
            document.getElementById('documentPreviewFrame').src = '';
            document.getElementById('documentPreviewModal').classList.add('hidden');
        }
    </script>

    <script>
        let termsScrolled = false;

        function openTermsModal() {
            document.getElementById('termsModal').classList.remove('hidden');
            document.getElementById('termsModal').classList.add('flex');
        }

        function closeTermsModal() {
            document.getElementById('termsModal').classList.add('hidden');
            document.getElementById('termsModal').classList.remove('flex');
        }

        document.addEventListener("DOMContentLoaded", () => {

            const scrollBox = document.getElementById('termsScrollBox');
            const checkbox = document.getElementById('termsCheckbox');
            const submitBtn = document.getElementById('finalSubmitBtn');

            scrollBox.addEventListener('scroll', () => {
                if (scrollBox.scrollTop + scrollBox.clientHeight >= scrollBox.scrollHeight - 5) {
                    termsScrolled = true;
                    checkbox.disabled = false;
                }
            });

            checkbox.addEventListener('change', () => {
                submitBtn.disabled = !(termsScrolled && checkbox.checked);
            });
        });
    </script>


</div>
