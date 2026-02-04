<x-layouts.auth>
    <div class="max-w-xl mx-auto bg-white rounded-lg shadow border">

        {{-- HEADER --}}
        <div class="px-6 py-4 border-b">
            <h2 class="text-xl font-semibold text-gray-800">
                Registration Successful !
            </h2>
        </div>

        {{-- BODY --}}
        <div class="px-6 py-6 space-y-5 text-gray-700 text-sm">

            <p>
                Before proceeding, please check your registered email for
                <span class="text-red-600 font-semibold">Temporary Password</span>
            </p>

            <p>
                If you did not receive the email, please contact Pre Exam Support :
            </p>

            <p class="text-center">
                <a href="mailto:preexam@rguhs.ac.in"
                   class="text-blue-600 font-semibold">
                    itsupport@rguhs.ac.in
                </a>
            </p>

            <p class="text-center text-red-600 font-semibold">
                Note: Please check your "Spam" folder
            </p>
        </div>

        {{-- FOOTER --}}
        <div class="px-6 py-4 border-t text-center">
            <a href="{{ url('/') }}"
               class="text-blue-600 font-semibold hover:underline">
                Home
            </a>
        </div>

    </div>
</x-layouts.auth>
