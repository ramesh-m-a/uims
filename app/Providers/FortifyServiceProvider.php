<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Models\Admin\User;
use App\Support\Captcha;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();

        /**
         * ==================================================
         * 🔐 CAPTCHA VALIDATION (BEFORE AUTH)
         * ==================================================
         * This is the CRITICAL FIX.
         * Captcha must run BEFORE AttemptToAuthenticate.
         */
        Fortify::authenticateThrough(function () {
            return [
                function (Request $request, $next) {

                    if (!Captcha::verify($request->input('captcha'))) {
                        throw ValidationException::withMessages([
                            'captcha' => 'Invalid Captcha',
                        ]);
                    }

                    // 🔥 Clear captcha after one use
                    Captcha::clear();

                    return $next($request);
                },

                // Actual Fortify authentication
                AttemptToAuthenticate::class,
            ];
        });

        /**
         * ==================================================
         * 👤 USER AUTHENTICATION
         * ==================================================
         */
        Fortify::authenticateUsing(function (Request $request) {

            $user = User::where('email', $request->email)->first();

            if (
                $user &&
                $user->user_status_id == 1 && // ACTIVE
                Hash::check($request->password, $user->password)
            ) {
                return $user;
            }

            return null;
        });
    }

    /**
     * ==================================================
     * ACTIONS
     * ==================================================
     */
    private function configureActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::createUsersUsing(CreateNewUser::class);
    }

    /**
     * ==================================================
     * VIEWS (GET REQUESTS ONLY)
     * ==================================================
     */
    private function configureViews(): void
    {
        Fortify::loginView(function () {

            // ✅ Generate captcha ONLY if not already present
            if (!Captcha::get()) {
                Captcha::generate();
            }

            return view('livewire.auth.login', [
                'captcha' => Captcha::get(),
            ]);
        });

        Fortify::verifyEmailView(fn () => view('livewire.auth.verify-email'));
        Fortify::twoFactorChallengeView(fn () => view('livewire.auth.two-factor-challenge'));
        Fortify::confirmPasswordView(fn () => view('livewire.auth.confirm-password'));
        Fortify::registerView(fn () => view('auth.register'));
        Fortify::resetPasswordView(fn () => view('livewire.auth.reset-password'));
        Fortify::requestPasswordResetLinkView(fn () => view('livewire.auth.forgot-password'));
    }

    /**
     * ==================================================
     * RATE LIMITING
     * ==================================================
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by(
                $request->session()->get('login.id')
            );
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(
                Str::lower($request->input(Fortify::username())) . '|' . $request->ip()
            );

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}
