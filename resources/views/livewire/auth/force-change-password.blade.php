@extends('layouts.guest')

@section('content')
    <div class="container min-vh-100 d-flex align-items-center justify-content-center">
        <div class="row w-100 justify-content-center">
            <div class="col-md-6 col-lg-5 col-xl-4">

                <div class="card shadow-sm">
                    <div class="card-header">
                        <strong>Force Password Change</strong>
                    </div>

                    <div class="card-body">

                        <p class="text-muted mb-3">
                            You must change your password before continuing......
                        </p>

                        <form method="POST" action="{{ route('force-password.update') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-control"
                                    required
                                >
                                @error('password')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input
                                    type="password"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    class="form-control"
                                    required
                                >
                                <small id="matchMessage" class="text-danger d-none">
                                    Passwords do not match
                                </small>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button
                                    type="submit"
                                    id="submitBtn"
                                    class="btn btn-white"
                                    disabled
                                >
                                    Update Password
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Fix disabled hover + click behavior --}}
    <style>
        button:disabled {
            pointer-events: none;      /* truly unclickable */
            opacity: 0.6;              /* visually disabled */
        }
    </style>

    {{-- JS --}}
    <script>
        const password = document.getElementById('password');
        const confirm  = document.getElementById('password_confirmation');
        const button   = document.getElementById('submitBtn');
        const message  = document.getElementById('matchMessage');

        function validatePasswords() {
            const pass = password.value.trim();
            const conf = confirm.value.trim();

            if (!pass || !conf) {
                button.disabled = true;
                message.classList.add('d-none');
                return;
            }

            if (pass === conf) {
                button.disabled = false;
                message.classList.add('d-none');
            } else {
                button.disabled = true;
                message.classList.remove('d-none');
            }
        }

        password.addEventListener('input', validatePasswords);
        confirm.addEventListener('input', validatePasswords);
    </script>
@endsection
