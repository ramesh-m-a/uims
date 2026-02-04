<?php

namespace App\Livewire\Auth;

use App\Models\Admin\User;
use App\Models\Master\Config\Academic\College;
use App\Models\Master\Config\Academic\Designation;
use App\Models\Master\Config\Academic\Stream;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;

class Register extends Component
{
    /* ================= FORM FIELDS ================= */
    public $mobile;
    public $user_stream_id;
    public $user_college_id;
    public $user_designation_id;
    public $email;
    public $name;

    /* ================= UI STATE ================= */
    public $mobileChecked = false;
    public $existingUser  = false;

    /* ================= DROPDOWNS ================= */
    public $streams = [];
    public $colleges = [];
    public $designations = [];

    /* ==================================================
     | SINGLE SOURCE OF TRUTH (LIVEWIRE 3 SAFE)
     ================================================== */
    public function updated($property, $value)
    {
        /* ================= MOBILE ================= */
        if ($property === 'mobile') {

            // Reset until exactly 10 digits
            if (strlen($value) < 10) {
                $this->resetForm();
                return;
            }

            if (!preg_match('/^[6-9][0-9]{9}$/', $value)) {
                return;
            }

            // Prevent refiring
            if ($this->mobileChecked) {
                return;
            }

            $this->mobileChecked = true;

            $user = User::with(['stream', 'college', 'designation'])
                ->where('mobile', $value)
                ->first();

            if ($user) {
                /* ===== EXISTING USER ===== */
                $this->existingUser = true;

                $this->streams = Stream::orderBy('mas_stream_name')->get();
                $this->colleges = College::where(
                    'mas_college_stream_id',
                    $user->user_stream_id
                )->orderBy('mas_college_name')->get();
                $this->designations = Designation::orderBy('mas_designation_name')->get();

                $this->user_stream_id      = $user->user_stream_id;
                $this->user_college_id     = $user->user_college_id;
                $this->user_designation_id = $user->user_designation_id;

                $this->email = $user->email;
                $this->name  = $user->name;
                return;
            }

            /* ===== NEW USER ===== */
            $this->existingUser = false;
            $this->streams = Stream::orderBy('mas_stream_name')->get();
        }

        /* ================= STREAM → COLLEGE ================= */
        if ($property === 'user_stream_id' && !$this->existingUser) {

            $this->user_college_id = null;
            $this->user_designation_id = null;
            $this->designations = [];

            if (!$value) {
                $this->colleges = [];
                return;
            }

            $this->colleges = College::where(
                'mas_college_stream_id',
                $value
            )->orderBy('mas_college_name')->get();
        }

        /* ================= COLLEGE → DESIGNATION ================= */
        if ($property === 'user_college_id' && !$this->existingUser) {

            $this->user_designation_id = null;

            if (!$value) {
                $this->designations = [];
                return;
            }

            $this->designations = Designation::orderBy('mas_designation_name')->get();
        }

        /* ================= EMAIL (REALTIME CHECK) ================= */
        if ($property === 'email' && !$this->existingUser) {

            if (User::where('email', $value)->exists()) {
                $this->addError(
                    'email',
                    'This email is already registered in RGUHS Portal'
                );
            } else {
                $this->resetErrorBag('email');
            }
        }
    }

    /* ==================================================
     | REGISTER
     ================================================== */
    public function register()
    {
        /* ==============================
         | 🚦 THROTTLE CHECK
         ============================== */
        $key = $this->throttleKey();

        if (RateLimiter::tooManyAttempts($key, 5)) {

            AuditLogger::log(
                table: 'users',
                recordId: null,
                action: 'register_throttled',
                oldValues: null,
                newValues: [
                    'mobile' => $this->mobile,
                    'ip'     => request()->ip(),
                ]
            );

            $seconds = RateLimiter::availableIn($key);

            $this->addError(
                'mobile',
                "Too many registration attempts. Please try again in {$seconds} seconds."
            );

            return;
        }

        RateLimiter::hit($key, 600); // 10 minutes

        /* ==============================
         | 🔒 EXISTING USER (BY MOBILE)
         ============================== */
        if ($this->existingUser) {

            AuditLogger::log(
                'users',
                null,
                'register_failed',
                null,
                [
                    'reason' => 'mobile_exists',
                    'mobile' => $this->mobile,
                    'email'  => $this->email,
                ]
            );

            $this->addError(
                'email',
                'This email is already registered in RGUHS Portal'
            );

            return;
        }

        /* ==============================
         | ✅ VALIDATION
         ============================== */
        $validated = $this->validate([
            'mobile'               => ['required', 'digits:10', 'regex:/^[6-9][0-9]{9}$/'],
            'email'                => 'required|email',
            'name'                 => ['required', 'string', 'max:100', 'regex:/^[A-Z\s]+$/'],
            'user_stream_id'       => 'required',
            'user_college_id'      => 'required',
            'user_designation_id'  => 'required',
        ]);

        /* ==============================
         | 🔥 EMAIL EXISTS CHECK
         ============================== */
        if (User::where('email', $this->email)->exists()) {

            AuditLogger::log(
                'users',
                null,
                'register_failed',
                null,
                [
                    'reason' => 'email_exists',
                    'email'  => $this->email,
                    'mobile' => $this->mobile,
                ]
            );

            $this->addError(
                'email',
                'This email is already registered in RGUHS Portal'
            );

            return;
        }

        /* ==============================
         | 🔐 CREATE USER
         ============================== */
        /* ==============================
       | CREATE USER (FINAL FIX)
       ============================== */
        $tempPassword = config('app.temp_user_password');

        // DEFAULT ROLE = TEACHER
        $teacherRoleId = \App\Models\Admin\Role::where('name', 'my-details')->value('id');

        if (!$teacherRoleId) {
            throw new \RuntimeException('Teacher role not found. Seeder missing.');
        }

        $user = User::create([
            'name'                 => $this->name,
            'email'                => $this->email,
            'mobile'               => $this->mobile,

            'user_stream_id'       => $this->user_stream_id,
            'user_college_id'      => $this->user_college_id,
            'user_designation_id'  => $this->user_designation_id,

            // 🔥 CRITICAL FIXES
            'user_role_id'         => $teacherRoleId,
            'user_status_id'       => 1, // ACTIVE

            'password'             => Hash::make($tempPassword),
            'force_password_change'=> 1,
        ]);

        /* ==============================
         | 📝 AUDIT SUCCESS
         ============================== */
        AuditLogger::log(
            'users',
            $user->id,
            'create',
            null,
            [
                'email'   => $user->email,
                'mobile'  => $user->mobile,
                'stream'  => $user->user_stream_id,
                'college' => $user->user_college_id,
            ]
        );

        /* ==============================
         | 📧 SEND MAIL
         ============================== */
        // 📧 SEND TEMP PASSWORD EMAIL (🔥 FIXED)
        try {
            Mail::send('emails.temp-password', [
                'user'         => $user,
                'tempPassword' => $tempPassword, // ✅ MUST MATCH BLADE
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('RGUHS UIMS – Temporary Password');
            });
        } catch (\Throwable $e) {
            logger()->error('Temp password email failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
        }

        /* ==============================
         | 🧹 CLEAR THROTTLE ON SUCCESS
         ============================== */
        RateLimiter::clear($key);

        return redirect()->route('register.success');
    }


    /* ==================================================
     | RESET HELPER
     ================================================== */
    private function resetForm()
    {
        $this->mobileChecked = false;
        $this->existingUser = false;

        $this->streams = [];
        $this->colleges = [];
        $this->designations = [];

        $this->user_stream_id = null;
        $this->user_college_id = null;
        $this->user_designation_id = null;

        $this->email = null;
        $this->name = null;
    }

    public function render()
    {
        return view('livewire.auth.register');
    }

    protected function throttleKey(): string
    {
        return 'register:' . request()->ip() . ':' . $this->mobile;
    }
}
