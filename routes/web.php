<?php

use App\Http\Controllers\Admin\TeacherApprovalController;
use App\Http\Controllers\Audit\ProfileAuditController;
use App\Http\Controllers\Auth\ForcePasswordController;
use App\Http\Controllers\Examiner\AppointmentOrderVerifyController;
use App\Http\Controllers\ExaminerAllocationTestController;

use App\Http\Controllers\ExaminerController;
use App\Http\Controllers\ExaminerRequestController;
use App\Http\Controllers\IdVerifyController;
use App\Http\Controllers\Master\StudentPerBatch\StudentPerBatchController;
use App\Http\Controllers\ProfilePreviewController;
use App\Http\Controllers\Rguhs\ProfileApprovalController as RguhsProfileApprovalController;
use App\Http\Controllers\SubjectDetailsController;
use App\Http\Controllers\VerifyIdController;
use App\Livewire\Examiner\Allocation\AllocationTable;
use App\Livewire\Examiner\Allocation\ExaminerAllocationCollege;
use App\Livewire\Examiner\Appoint\AppointExaminer;

use App\Livewire\Examiner\AppointmentOrder\AppointmentOrderView;
use App\Livewire\Examiner\Requests\RequestQueueTable;
use App\Livewire\Profile\IdCard;
use App\Livewire\Profile\IdCardControllerdelete;
use App\Livewire\Profile\SubjectDetailsTable;
use App\Livewire\User\UserTable;
use App\Models\Export;
use App\Models\Master\Config\Academic\DegreeStream;
use App\Services\Dashboard\DashboardRegistry;
use App\Support\Captcha;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

use Firebase\JWT\JWT;
use Spatie\Browsershot\Browsershot;

/* =========================
 | SUPPORT / LIVEWIRE
 ========================= */

/* =========================
 | CONTROLLERS
 ========================= */

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => redirect()->route('login'));
Route::get('/', fn () => redirect()->route('login'));

Route::get('/refresh-captcha', function () {
    Captcha::refresh();
    return redirect()->route('login');
})->name('captcha.refresh');

Route::view('/register-success', 'auth.register-success')
    ->name('register.success');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED (BASE)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'force.password.change',])->group(function () {

    /* Dashboard */
    /*Route::view('/dashboard', 'dashboard')->name('dashboard');*/

    Route::get('/dashboard', function () {
        $dashboard = (new DashboardRegistry())->build(
            auth()->user()->role ?? 'admin',
            [
                'college_id'   => auth()->user()->user_college_id ?? null,
                'principal_id' => auth()->id(),
            ]
        );

        return view('dashboard', compact('dashboard'));
    })->name('dashboard');

    /* Settings (Volt) */
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                && Features::optionEnabled(
                    Features::twoFactorAuthentication(),
                    'confirmPassword'
                ),
                ['password.confirm'],
                []
            )
        )
        ->name('two-factor.show');

    /* Force password change */
    Route::get('/force-password-change', [ForcePasswordController::class, 'edit'])
        ->name('force-password.change');

    Route::post('/force-password-change', [ForcePasswordController::class, 'update'])
        ->name('force-password.update');
});

/*
|--------------------------------------------------------------------------
| TEACHER – PROFILE FLOW (🔥 SINGLE SOURCE OF TRUTH)
|--------------------------------------------------------------------------
*/

/**
 * EDIT PROFILE (LIVEWIRE WIZARD)
 * ✔ Draft-only
 * ✔ Locked via middleware
 */



Route::get('/my-details/edit', \App\Livewire\Profile\EditProfileWizard::class)
    ->middleware('auth')
    ->name('profile.edit.wizard');


/**
 * PREVIEW PROFILE (PLAIN BLADE)
 * ✔ Read-only
 * ✔ Shows exactly what will be submitted
 */
Route::middleware(['auth'])
    ->get('/my-details/preview', [ProfilePreviewController::class, 'show'])
    ->name('profile.preview');

/**
 * SUBMIT PROFILE
 * ✔ Only from preview
 * ✔ Calls Commit Service
 */
Route::middleware(['auth'])
    ->post('/my-details/submit', [ProfilePreviewController::class, 'submit'])
    ->name('profile.submit');

Route::get('/_debug/principal', function () {
    $user = auth()->user();

    return [
        'id' => $user->id,
        'roles' => $user->roles->pluck('name'),
        'permissions' => $user->cachedPermissions(),
        'has_principal_role' => $user->hasRole('principal'),
        'can_principal' => $user->can('principal'),
    ];
})->middleware('auth');

/*
|--------------------------------------------------------------------------
| ADMIN / APPROVAL FLOWS
|--------------------------------------------------------------------------
*/

/* Admin – Teacher list */
/*
|--------------------------------------------------------------------------
| ADMIN / APPROVAL FLOWS (ADMIN = user_role_id NULL)
|--------------------------------------------------------------------------
*/

/* Admin – Teacher list */
/*
|--------------------------------------------------------------------------
| ADMIN / APPROVAL FLOWS (SECURED WITH RBAC)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'permission:admin.teacher.view'])->group(function () {
    Route::view('/admin/teachers', 'admin.teacher.index')
        ->name('admin.teacher.index');
});

Route::middleware(['auth', 'permission:admin.examiners.view'])->group(function () {
    Route::view('/admin/examiners', 'admin.examiners.index')
        ->name('admin.examiners.index');
});

Route::middleware(['auth', 'permission:admin.principals.view'])->group(function () {
    Route::view('/admin/principals', 'admin.principals.index')
        ->name('admin.principals.index');
});

/* Admin – Teacher view (read-only) */
Route::middleware(['auth'])
    ->get('/admin/teachers/{user}', function (\App\Models\Admin\User $user) {

        $draft = $user->profileDraft;

        abort_if(! $draft || ! $draft->submitted_at, 404);

        return view('admin.my-details-view', compact('user', 'draft'));
    })
    ->name('admin.teachers.view');

/* Admin – Actions */
Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::post(
            '/teachers/{user}/action',
            [TeacherApprovalController::class, 'action']
        )->name('teachers.action');
    });


/* Principal approvals */
Route::middleware(['auth', 'has.role:principal'])
    ->prefix('principal')
    ->name('principal.')
    ->group(function () {

        Route::get('/profiles/{user}',
            [\App\Http\Controllers\Principal\ProfileApprovalController::class, 'show']
        )->name('profiles.show');

        Route::post('/profiles/{user}/approve',
            [\App\Http\Controllers\Principal\ProfileApprovalController::class, 'approve']
        )->name('profiles.approve');

        Route::post('/profiles/{user}/reject',
            [\App\Http\Controllers\Principal\ProfileApprovalController::class, 'reject']
        )->name('profiles.reject');
    });



/* RGUHS approvals */
Route::middleware(['auth', 'can:rguhs'])
    ->prefix('rguhs')
    ->name('rguhs.')
    ->group(function () {

        Route::post('/profiles/{user}/approve',
            [RguhsProfileApprovalController::class, 'approve']
        )->name('profiles.approve');

        Route::post('/profiles/{user}/reject',
            [RguhsProfileApprovalController::class, 'reject']
        )->name('profiles.reject');
    });

/*
|--------------------------------------------------------------------------
| AUDIT
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])
    ->get('/teachers/{user}/audit', [ProfileAuditController::class, 'show'])
    ->name('teachers.audit');

/*
|--------------------------------------------------------------------------
| PROTECTED AREA (ROLE + PERMISSION) – UNCHANGED
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'has.role', 'force.password.change'])->group(function () {

    /* Users */
    Route::middleware('permission:user.view')->group(function () {
        Route::get('/user', UserTable::class)->name('user.index');
    });

    Route::middleware('permission:user.edit')->group(function () {
        Route::get('/user/{user}/roles',
            \App\Livewire\User\UserRoleAssignment::class
        )->name('user.roles');
    });

    /* Examiner */
    Route::middleware('permission:exam.view')->group(function () {
        Route::get('/exam-details', [ExaminerController::class, 'index'])
            ->name('exam.index');
    });

    /* Student per batch */
    Route::get('/master/student-per-batch',
        [StudentPerBatchController::class, 'index']
    )->name('master.student-per-batch.index');


});

/*
|--------------------------------------------------------------------------
| PROTECTED AREA (COMMON)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'permission:master.common.view'])
    ->prefix('master/common')
    ->as('master.common.')
    ->group(function () {

        Route::view('/year', 'master.common.year.index')
            ->name('year.index');

        Route::view('/month', 'master.common.month.index')
            ->name('month.index');

        Route::view('/gender', 'master.common.gender.index')
            ->name('gender.index');

        Route::view('/religion', 'master.common.religion.index')
            ->name('religion.index');

        Route::view('/nationality', 'master.common.nationality.index')
            ->name('nationality.index');

        Route::view('/category', 'master.common.category.index')
            ->name('category.index');

        Route::view('/document', 'master.common.document.index')
            ->name('document.index');

        Route::view('/bank', 'master.common.bank.index')
            ->name('bank.index');

        Route::view('/bank-branch', 'master.common.bank-branch.index')
            ->name('bank-branch.index');

        Route::view('/ifsc', 'master.common.ifsc.index')
            ->name('ifsc.index');

        Route::view('/state', 'master.common.state.index')
            ->name('state.index');

        Route::view('/district', 'master.common.district.index')
            ->name('district.index');

        Route::view('/city', 'master.common.city.index')
            ->name('city.index');

        Route::view('/taluk', 'master.common.taluk.index')
            ->name('taluk.index');

        Route::view('/status', 'master.common.status.index')
            ->name('status.index');

        Route::view('/salary_mode', 'master.common.salary-mode.index')
            ->name('salary-mode.index');

        Route::view('/account_type', 'master.common.account-type.index')
            ->name('account-type.index');
    });

/*
|--------------------------------------------------------------------------
| ROLE – PERMISSIONS
|--------------------------------------------------------------------------
*/

Route::view('/master/role', 'master.role.index')
    ->middleware('permission:master.role.view')
    ->name('master.role.index');

Route::get(
    '/master/role/{role}/permissions',
    \App\Livewire\Master\Role\RolePermissionTable::class
)
    ->middleware('permission:master.role.view') // ✅ view allowed
    ->name('master.role.permissions');


/*
|--------------------------------------------------------------------------
| MASTER – CONFIG
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission:master.config.academic.view'])
    ->prefix('master/config/academic')
    ->as('master.config.academic.')
    ->group(function () {

        Route::view('/stream', 'master.config.academic.stream.index')
            ->name('stream.index');

        Route::view('/college', 'master.config.academic.college.index')
            ->name('college.index');

        Route::view('/degree', 'master.config.academic.degree.index')
            ->name('degree.index');

        Route::view('/department', 'master.config.academic.department.index')
            ->name('department.index');

        Route::view('/designation', 'master.config.academic.designation.index')
            ->name('designation.index');

        Route::view('/subject', 'master.config.academic.subject.index')
            ->name('subject.index');


    });

/*
|--------------------------------------------------------------------------
| USERS
|--------------------------------------------------------------------------
*/
Route::middleware('permission:user.view')->group(function () {
    Route::get('/user', UserTable::class)->name('user.index');
});

Route::middleware('permission:user.edit')->group(function () {
    Route::get('/user/{user}/roles', \App\Livewire\User\UserRoleAssignment::class)
        ->name('user.roles');
});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'permission:master.role.view'])
    ->prefix('master')
    ->as('master.')
    ->group(function () {

        Route::view('/role', 'master.role.index')
            ->name('role.index');
    });

Route::middleware(['auth', 'permission:user.edit'])
    ->get(
        '/user/{user}/permissions',
        \App\Livewire\User\UserPermissionOverride::class
    )
    ->name('user.permissions');

Route::get(
    '/user/{user}/permissions/copy',
    \App\Livewire\User\UserPermissionBulkCopy::class
)->middleware('permission:user.edit')
    ->name('user.permissions.copy');

Route::middleware(['auth', 'permission:master.role.edit'])
    ->prefix('admin/permission-templates')
    ->name('permission-templates.')
    ->group(function () {

        Route::view('/', 'admin.permission-template.index')
            ->name('index');

        Route::get('/create',
            \App\Livewire\Admin\PermissionTemplate\TemplateEditor::class
        )->name('create');

        Route::get('/{template}/edit',
            \App\Livewire\Admin\PermissionTemplate\TemplateEditor::class
        )->name('edit');
    });

Route::get(
    '/master/role/{role}/apply-template',
    \App\Livewire\Master\Role\ApplyTemplateToRole::class
)->middleware('permission:master.role.edit')
    ->name('master.role.apply-template');

Route::get(
    '/admin/permission-templates/audit',
    \App\Livewire\Admin\PermissionTemplate\TemplateAuditTable::class
)->middleware('permission:master.role.view')
    ->name('permission-templates.audit');

Route::middleware(['auth'])
    ->get('/my-details', \App\Livewire\Profile\MyDetailsTable::class)
    ->name('my-details.index');





/*Route::get('/users/{user}/admin-roles', AdminRoleAssignment::class)
    ->middleware('auth')
    ->name('users.admin-roles');*/


/*Route::get(
    '/master/config/academic/degree-stream',
    DegreeStream::class
)->name('master.config.academic.degree-stream');*/

/*Route::get(
    '/master/config/academic/degree-specialisation',
    DegreeSpecialisatonTable::class
)->name('master.config.academic.degree-specialisation');*/


Route::middleware(['auth'])->group(function () {

    Route::view('/batch', 'master.config.exam.batch.index')
        ->name('batch.index');

    Route::view('/batch-range', 'master.config.exam.batch-range.index')
        ->name('batch-range.index');

    Route::view('/batch-split', 'master.config.exam.batch-split.index')
        ->name('batch-split.index');

    Route::view('/revised-scheme', 'master.config.exam.revised-scheme.index')
        ->name('revised-scheme.index');

    Route::view('/examiner-scheme-distribution', 'master.config.exam.examiner-scheme-distribution.index')
        ->name('examiner-scheme-distribution.index');

    Route::view('/student-batch-distribution', 'master.config.exam.examiner-scheme-distribution.index')
        ->name('student-batch-distribution.index');

});

Route::middleware(['auth'])->group(function () {
    Route::get('/debug-shortfall', function () {
        $state = app(\App\Services\ExaminerAllocation\Domain\AllocationState::class);
        dd($state->shortfalls);
    });
    Route::get('/allocation-test', [ExaminerAllocationTestController::class, 'test']);

    Route::get('/allocation-finalize-test', [ExaminerAllocationTestController::class, 'finalizeTest']);

    Route::get('/examiner/allocation', AllocationTable::class)
        ->name('examiner.allocation');

    Route::get('/examiner/appoint', AppointExaminer::class)
        ->name('examiner.appoint');

    Route::get('/examiner/college/allocation', ExaminerAllocationCollege::class)
        ->name('examiner.college.allocation');

    Route::get('/examiner/appointment-order/view', AppointmentOrderView::class)
        ->name('examiner.appointment-order.view');

});

Route::middleware(['auth'])->group(function () {
    Route::get('/subject-details', SubjectDetailsTable::class)
        ->name('profile.subject-details');

    Route::get('/partials-card', IdCard::class)
        ->name('profile.partials-card');

    Route::get('/verify', VerifyIdController::class)->name('verify.id');
/*
    Route::get('/verify/{token}', [IdVerifyController::class, 'verify'])
        ->name('id.verify');*/
});




Route::get('/test-token', function () {
    $user = auth()->user() ?? \App\Models\Admin\User::first();

    $payload = [
        'sub' => $user->id,
        'tin' => $user->tin ?? 'TEST123',
        'exp' => time() + 3600, // valid for 1 hour
        'device' => sha1(request()->userAgent() ?? ''),
    ];

    $token = JWT::encode($payload, config('app.key'), 'HS256');

    return url('/verify?token=' . $token);
});


Route::get('/exports/{export}/download', function (Export $export) {
    abort_unless($export->user_id === auth()->id(), 403);

    return Storage::disk($export->disk)
        ->download($export->filename);
})->name('exports.download');






Route::prefix('examiner')->group(function () {

    Route::get('/requests', [ExaminerRequestController::class, 'index'])
        ->name('examiner.requests');

    Route::post('/examiner/requests/{id}/approve',
        [ExaminerRequestController::class, 'approve']
    )->name('examiner.requests.approve');

    Route::post('/examiner/requests/{id}/reject',
        [ExaminerRequestController::class, 'reject']
    )->name('examiner.requests.reject');

    Route::get('/examiner/requests', RequestQueueTable::class)
        ->name('examiner.requests');


});


Route::get('/test-pdf', function () {

    $html = "
        <h1>UIMS PDF Test</h1>
        <p>If you can read this → Chromium + Browsershot working</p>
    ";

    $path = storage_path('app/test.pdf');

    Browsershot::html($html)
        ->format('A4')
        ->timeout(120)
        ->save($path);

    return "PDF Generated at storage/app/test.pdf";
});

Route::get('/verify-order/{orderNumber}',
    [AppointmentOrderVerifyController::class, 'verify']
);


/*Route::prefix('examiner/appointment-order')->group(function () {

    Route::get('/view', [AppointmentOrderViewController::class, 'index'])
        ->name('examiner.appointment-order.view');

    Route::get('/list', [AppointmentOrderViewController::class, 'list'])
        ->name('examiner.appointment-order.list');

});*/

/*

Route::get('/examiner/appoint', AppointExaminer::class)->name('examiner.appoint');

Route::get('/examiner/appoint', [ExaminerAppointController::class, 'appoint'])
    ->name('examiner.appoint');
Route::get('/examiner/allocation', function () {
    return view('examiner.allocation'); // or your existing Livewire page
})->name('examiner.allocation');


*/
