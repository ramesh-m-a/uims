<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class MenuSidebar extends Component
{
    public array $menus = [];

    private const STATUS_PENDING = 26;
    private const STATUS_APPROVED = 27;
    private const STATUS_REJECTED = 28;

    public function mount()
    {
        $user = auth()->user();

        if (!$user) {
            return;
        }

       // dd($user->user_role_id);
        // -------------------------------------------------
        // FETCH EXAMINER REQUEST COUNTS (ONCE)
        // -------------------------------------------------
        $examinerCounts = DB::table('college_examiner_request_details')
            ->selectRaw("
        SUM(CASE WHEN college_examiner_request_details_status_id = ? THEN 1 ELSE 0 END) as pending_count,
        SUM(CASE WHEN college_examiner_request_details_status_id = ? THEN 1 ELSE 0 END) as approved_count,
        SUM(CASE WHEN college_examiner_request_details_status_id = ? THEN 1 ELSE 0 END) as rejected_count
    ", [
                self::STATUS_PENDING,
                self::STATUS_APPROVED,
                self::STATUS_REJECTED
            ])
            ->first();

        $pendingCount  = $examinerCounts->pending_count ?? 0;
        $approvedCount = $examinerCounts->approved_count ?? 0;
        $rejectedCount = $examinerCounts->rejected_count ?? 0;

        /**
         * FINAL RULES (MATCH DB TRUTH)
         * - user_role_id === NULL  => ADMIN
         * - user_role_id === 5     => TEACHER
         */
        $isAdmin   = is_null($user->user_role_id);
        $isTeacher = $user->user_role_id === 5;
        $isCollege = $user->user_role_id === 3;

        $urlFor = fn (?string $route)
        => $route && Route::has($route) ? route($route) : '#';

        $isActive = fn (string $pattern)
        => request()->routeIs($pattern);

        /* ==================================================
         | 🔥 ADMIN MENU (FULL)
         ================================================== */
        $adminMenus = [

            [
                'title' => 'Home',
                'type'  => 'group',
                'items' => [
                    [
                        'title'  => 'Dashboard',
                        'icon'   => 'dashboard',
                        'href'   => $urlFor('dashboard'),
                        'active' => $isActive('dashboard'),
                    ],
                ],
            ],

            [
                'title' => 'Masters',
                'type'  => 'group',
                'items' => [

                    [
                        'title' => 'Admin',
                        'icon'  => 'gear',
                        'children' => [
                            [
                                'title' => 'Role',
                                'icon'  => 'user-shield', // users-cog, id-badge users-gear
                                'href'  => $urlFor('master.role.index'),
                                'active'=> $isActive('master.role.*'),
                            ],
                            [
                                'title' => 'User',
                                'icon'  => 'user',
                                'href'  => $urlFor('user.index'),
                                'active'=> $isActive('user.*'),
                            ],
                        ],
                    ],

                    [
                        'title' => 'Academic',
                        'icon'  => 'layer-group',
                        'children' => [
                            [
                                'title' => 'Stream',
                                'icon'  => 'diagram-project',
                                'href'  => $urlFor('master.config.academic.stream.index'),
                                'active'=> $isActive('master.config.academic.stream.*'),
                            ],
                            [
                                'title' => 'College',
                                'icon'  => 'building-columns',
                                'href'  => $urlFor('master.config.academic.college.index'),
                                'active'=> $isActive('master.config.academic.college.*'),
                            ],
                            [
                                'title' => 'Designation',
                                'icon'  => 'user-tie',
                                'href'  => $urlFor('master.config.academic.designation.index'),
                                'active'=> $isActive('master.config.academic.designation.*'),
                            ],
                            [
                                'title' => 'Department',
                                'icon'  => 'sitemap',
                                'href'  => $urlFor('master.config.academic.department.index'),
                                'active'=> $isActive('master.config.academic.department.*'),
                            ],
                            [
                                'title' => 'Degree',
                                'icon'  => 'graduation-cap',
                                'href'  => $urlFor('master.config.academic.degree.index'),
                                'active'=> $isActive('master.config.academic.degree.*'),
                            ],
                           /* [
                                'title' => 'Degree Specialisation',
                                'icon'  => 'list-check',
                                'href'  => $urlFor('master.config.academic.degree-specialisation.index'),
                                'active'=> $isActive('master.config.academic.degree-specialisation.*'),
                            ],*/
                           /* [
                                'title' => 'Degree Stream Mapping',
                                'icon'  => 'link',*/
                                /* 'href'  => $urlFor('master.config.academic.degree-stream.index'),
                                 'active'=> $isActive('master.config.academic.degree-stream.*'),*/
                             /*   'href'  => '',
                                'active'=> '',
                            ],*/
                            [
                                'title' => 'Subject',
                                'icon'  => 'layer-group',
                                'href'  => $urlFor('master.config.academic.subject.index'),
                                'active'=> $isActive('master.config.academic.subject.*'),
                            ],
                        ],
                    ],

                    [
                        'title' => 'Exam Config',
                        'icon'  => 'user-secret',
                        'children' => [

                            [
                                'title' => 'Revised Scheme',
                                'icon'  => 'pen-to-square', //boxes
                                'href'   => $urlFor('revised-scheme.index'),
                                'active' => $isActive('revised-scheme.*'),
                            ],
                            [
                                'title' => 'Examiner Scheme Distribution',
                                //'title' => 'ESD',
                                'icon'  => 'diagram-project', // calendar-alt
                                'href'   => $urlFor('examiner-scheme-distribution.index'),
                                'active' => $isActive('examiner-scheme-distribution.*'),
                            ],
                            [
                                //'title' => 'Student Batch Distribution',
                                'title' => 'SBD',
                                'icon'  => 'users-gear', // random
                                'href'  => $urlFor('student-batch-distribution.index'),
                                'active'=> $isActive('student-batch-distribution.*'),
                            ],
                            [
                                'title'  => 'Batch',
                                'icon'   => 'layer-group',
                                'href'   => $urlFor('batch.index'),
                                'active' => $isActive('batch.*'),
                            ],
                            [
                                'title' => 'Batch Date Range',
                                'icon'  => 'calendar-days',
                                'href'   => $urlFor('batch-range.index'),
                                'active' => $isActive('batch-range.*'),
                            ],
                          /*  [
                                'title' => 'Batch Split',
                                'icon'  => 'code-branch',
                                'href'   => $urlFor('batch-split.index'),
                                'active' => $isActive('batch-split.*'),
                            ],*/
                        ],
                    ],

                    [
                        'title' => 'Finance',
                        'icon'  => 'indian-rupee',
                    ],

                    [
                        'title' => 'Common',
                        'icon'  => 'sliders',
                        'children' => [
                            [
                                'title'  => 'Year',
                                'icon'   => 'calendar-days',      // better than generic calendar
                                'href'   => $urlFor('master.common.year.index'),
                                'active' => $isActive('master.common.year.*'),
                            ],

                            [
                                'title'  => 'Month',
                                'icon'   => 'calendar-week',      // shows period/segment
                                'href'   => $urlFor('master.common.month.index'),
                                'active' => $isActive('master.common.month.*'),
                            ],

                            [
                                'title'  => 'Gender',
                                'icon'   => 'venus-mars',         // standard gender icon
                                'href'   => $urlFor('master.common.gender.index'),
                                'active' => $isActive('master.common.gender.*'),
                            ],

                            [
                                'title'  => 'Religion',
                                'icon'   => 'hands-praying',      // neutral + respectful
                                'href'   => $urlFor('master.common.religion.index'),
                                'active' => $isActive('master.common.religion.*'),
                            ],

                            [
                                'title'  => 'Nationality',
                                'icon'   => 'flag',               // perfect already
                                'href'   => $urlFor('master.common.nationality.index'),
                                'active' => $isActive('master.common.nationality.*'),
                            ],

                            [
                                'title'  => 'Category',
                                'icon'   => 'tags',               // better semantic than layer-group
                                'href'   => $urlFor('master.common.category.index'),
                                'active' => $isActive('master.common.category.*'),
                            ],

                            [
                                'title'  => 'Document',
                                'icon'   => 'file-lines',         // more specific than plain file
                                'href'   => $urlFor('master.common.document.index'),
                                'active' => $isActive('master.common.document.*'),
                            ],

                            [
                                'title'  => 'Bank',
                                'icon'   => 'building-columns',   // proper FA6 bank icon
                                'href'   => $urlFor('master.common.bank.index'),
                                'active' => $isActive('master.common.bank.*'),
                            ],

                            [
                                'title'  => 'Bank Branch',
                                'icon'   => 'code-branch',        // branch representation
                                'href'   => $urlFor('master.common.bank-branch.index'),
                                'active' => $isActive('master.common.bank-branch.*'),
                            ],

                            [
                                'title'  => 'IFS Code',
                                'icon'   => 'barcode',            // better than generic code
                                'href'   => $urlFor('master.common.ifsc.index'),
                                'active' => $isActive('master.common.ifsc.*'),
                            ],

                            [
                                'title'  => 'State',
                                'icon'   => 'map',                // better geographic meaning
                                'href'   => $urlFor('master.common.state.index'),
                                'active' => $isActive('master.common.state.*'),
                            ],

                            [
                                'title'  => 'District',
                                'icon'   => 'map-location-dot',   // more specific
                                'href'   => $urlFor('master.common.district.index'),
                                'active' => $isActive('master.common.district.*'),
                            ],

                            [
                                'title'  => 'Taluk',
                                'icon'   => 'location-dot',       // smaller region
                                'href'   => $urlFor('master.common.taluk.index'),
                                'active' => $isActive('master.common.taluk.*'),
                            ],

                            [
                                'title'  => 'City',
                                'icon'   => 'city',               // literal match
                                'href'   => $urlFor('master.common.city.index'),
                                'active' => $isActive('master.common.city.*'),
                            ],

                            [
                                'title'  => 'Status',
                                'icon'   => 'circle-half-stroke', // clearer state indicator than circle-half
                                'href'   => $urlFor('master.common.status.index'),
                                'active' => $isActive('master.common.status.*'),
                            ],

                            [
                                'title'  => 'Salary Mode',
                                'icon'   => 'circle-half-stroke', // clearer state indicator than circle-half
                                'href'   => $urlFor('master.common.salary-mode.index'),
                                'active' => $isActive('master.common.salary-mode.*'),
                            ],

                            [
                                'title'  => 'Account Type',
                                'icon'   => 'circle-half-stroke', // clearer state indicator than circle-half
                                'href'   => $urlFor('master.common.account-type.index'),
                                'active' => $isActive('master.common.account-type.*'),
                            ],

                        ],
                    ],
                ],
            ],

            [
                'title' => 'Teachers',
                'type'  => 'group',
                'items' => [

                    [
                        'title' => 'All Teachers',
                        'icon'  => 'chalkboard-user',   // 👨‍🏫 perfect for teachers
                        'href'  => $urlFor('admin.teacher.index'),
                        'active'=> $isActive('admin.teacher.index'),
                    ],

                    [
                        'title' => 'All Principals',
                        'icon'  => 'user-tie',
                        'href'  => $urlFor('admin.principals.index'),
                        'active'=> $isActive('admin.principals.index'),
                    ],

                    // 👇 THIS BECOMES A NESTED GROUP
                    [
                        'title' => 'View Status',
                        'type'  => 'group',
                        'icon'  => 'eye',
                        'children' => [
                            [
                                'title' => 'Approval Pending',
                                'icon'  => 'hourglass-half',   // clearly means waiting
                                /* 'href'  => $urlFor('admin.applications.pending'),
                                 'active'=> $isActive('admin.applications.pending'),*/
                                'href'  => '',
                                'active'=> '',
                            ],
                            [
                                'title' => 'Approved',
                                'icon'  => 'circle-check',     // universally recognized approved
                                /* 'href'  => $urlFor('admin.applications.approved'),
                                 'active'=> $isActive('admin.applications.approved'),*/
                                'href'  => '',
                                'active'=> '',
                            ],
                            [
                                'title' => 'Rejected',
                                'icon'  => 'circle-xmark',     // clear rejected visual
                                /*  'href'  => $urlFor('admin.applications.rejected'),
                                  'active'=> $isActive('admin.applications.rejected'),*/
                                'href'  => '',
                                'active'=> '',
                            ],
                        ],
                    ],

                ],
            ],

            /*            'icon' => 'diagram-project',   // group
                        Pending  -> 'clock'
            Approved -> 'check-double'
            Rejected -> 'ban'*/
            [
                'title' => 'Examiner',
                'type'  => 'group',
                'items' => [

                    [
                        'title'  => 'All Examiner',
                        'icon'   => 'user-secret',
                        'href'  => $urlFor('admin.examiners.index'),
                        'active'=> $isActive('admin.examiners.index'),
                    ],

                    [
                        'title'  => 'Appoint Examiner',
                        'icon'   => 'user-plus',
                        'href'  => $urlFor('examiner.appoint'),
                        'active'=> $isActive('examiner.appoint'),
                    ],

                    [
                        'title'  => 'View All Examiner',
                        'icon'   => 'user-plus',
                        'href'  => $urlFor('examiner.college.appoint'),
                        'active'=> $isActive('examiner.college.appoint'),
                    ],

                    [
                        'title' => 'View Status',
                        'icon'  => 'eye',
                        'children' => [

                            [
                                'title' => 'Approval Pending',
                                'icon'  => 'hourglass-half',
                                'children' => [
                                    [
                                        'title'  => 'BOS',
                                        'icon'   => 'user-check',
                                        'href'   => '#',
                                        'active' => '#',
                                    ],
                                    [
                                        'title'  => 'Dean',
                                        'icon'   => 'user-check',
                                        'href'   => '',
                                        'active' => '',
                                    ],
                                    [
                                        'title'  => "College ({$pendingCount})",
                                        'icon'   => 'user-check',
                                        'href'   => $urlFor('examiner.requests') . '?status=' . self::STATUS_PENDING,
                                        'active' => request()->get('status') == self::STATUS_PENDING,
                                    ],
                                ],
                            ],

                            [
                                'title'  => 'Approved',
                                'icon'   => 'circle-check',
                                'children' => [
                                    [
                                        'title'  => 'BOS',
                                        'icon'   => 'user-check',
                                        'href'   => '#',
                                        'active' => '#',
                                    ],
                                    [
                                        'title'  => 'Dean',
                                        'icon'   => 'user-check',
                                        'href'   => '',
                                        'active' => '',
                                    ],
                                    [
                                        'title'  => "College ({$approvedCount})",
                                        'icon'   => 'user-check',
                                        'href'   => $urlFor('examiner.requests') . '?status=' . self::STATUS_APPROVED,
                                        'active' => request()->get('status') == self::STATUS_APPROVED,
                                    ],
                                ],
                            ],

                            [
                                'title'  => 'Rejected',
                                'icon'   => 'circle-xmark',
                                'children' => [
                                    [
                                        'title'  => 'BOS',
                                        'icon'   => 'user-xmark',
                                        'href'   => '#',
                                        'active' => '#',
                                    ],
                                    [
                                        'title'  => 'Dean',
                                        'icon'   => 'user-xmark',
                                        'href'   => '',
                                        'active' => '',
                                    ],
                                    [
                                        'title'  => "College ({$rejectedCount})",
                                        'icon'   => 'user-xmark',
                                        'href'   => $urlFor('examiner.requests') . '?status=' . self::STATUS_REJECTED,
                                        'active' => request()->get('status') == self::STATUS_REJECTED,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Remuneration',
                'type'  => 'group',
                'items' => [

                    [
                        'title'  => 'Entry / Claims',
                        'icon'   => 'file-invoice-dollar',
                        'href'   => '',
                        'active' => '',
                    ],

                    [
                        'title'  => 'View Status',
                        'icon'   => 'eye',
                        'children' => [

                            [
                                'title' => 'Approval Pending',
                                'icon'  => 'hourglass-half',
                            ],

                            [
                                'title'  => 'Approved',
                                'icon'   => 'circle-check',
                                'href'   => '',
                                'active' => '',
                            ],

                            [
                                'title'  => 'Rejected',
                                'icon'   => 'circle-xmark',
                            ],

                        ],
                    ],
                ],
            ],
            [
                'title' => 'Reports',
                'type'  => 'group',
                'items' => [

                    [
                        'title' => 'Examiner',
                        'icon'  => 'user-secret',
                        'children' => [

                            [
                                'title' => 'Appointment Order',
                                'icon'  => 'file-signature',
                                'children' => [
                                    [
                                        'title'  => 'Generate',
                                        'icon'   => 'file-circle-plus',
                                        'href'   => '',
                                        'active' => '',
                                    ],
                                    [
                                        'title'  => 'View',
                                        'icon'   => 'file-lines',
                                        'href'   => '',
                                        'active' => '',
                                    ],
                                ],
                            ],

                            [
                                'title'  => 'Chart',
                                'icon'   => 'chart-column',
                                'href'   => '',
                                'active' => '',
                            ],

                        ],
                    ],

                ],
            ],
        ];

        /* ==================================================
         | 👩‍🏫 TEACHER SELF MENU
         ================================================== */
        $teacherMenus = [

            [
                'title' => 'Home',
                'type'  => 'group',
                'items' => [
                    [
                        'title'  => 'Dashboard',
                        'icon'   => 'dashboard',
                        'href'   => $urlFor('dashboard'),
                        'active' => $isActive('dashboard'),
                    ],
                ],
            ],

            [
                'title' => 'My Details',
                'type'  => 'group',
                'items' => [
                    [
                        'title'  => 'Portfolio - View / Update',
                        'icon'   => 'user',
                        'href'   => route('my-details.index'),
                        'active' => request()->routeIs('my-details.*'),
                    ],
                    [
                        'title'  => 'Subject - View / Update',
                        'icon'   => 'user',
                        'href'   => $urlFor('profile.subject-details'),
                        'active' => $isActive('profile.subject-details'),
                    ],
                    [
                        'title'  => 'ID Card',
                        'icon'   => 'id-card',
                        'href'   => $urlFor('profile.partials-card'),
                        'active' => $isActive('profile.partials-card'),
                    ]
                ],
            ],
        ];

        /* ==================================================
 | 🏫 COLLEGE MENU
 ================================================== */
        $collegeMenus = [

            [
                'title' => 'Home',
                'type'  => 'group',
                'items' => [
                    [
                        'title'  => 'Dashboard',
                        'icon'   => 'dashboard',
                        'href'   => $urlFor('dashboard'),
                        'active' => $isActive('dashboard'),
                    ],
                ],
            ],

            [
                'title' => 'Examiner',
                'type'  => 'group',
                'items' => [

                    [
                        'title'  => 'Appoint Examiner',
                        'icon'   => 'user-plus',
                        'href'  => $urlFor('examiner.appoint'),
                        'active'=> $isActive('examiner.appoint'),
                    ],
                    [
                        'title'  => 'My Change Requests',
                        'icon'   => 'envelope',
                        'href'   => '#', // future
                        'active' => '',
                    ],

                ],
            ],

        ];


        /* ==================================================
         | 🎯 FINAL ASSIGNMENT (NO TRIMMING)
         ================================================== */
        if ($isAdmin) {
            $this->menus = $adminMenus;
            return;
        }

        if ($isTeacher) {
            $this->menus = $teacherMenus;
            return;
        }

        /**
         * ⭐ COLLEGE USERS
         * For now reuse admin menu (safe + fast)
         * Later we can restrict if needed
         */
        if ($isCollege) {
            $this->menus = $collegeMenus;
            return;
        }

        // fallback (safe)
        $this->menus = [$adminMenus[0]];
    }

    public function render()
    {
        return view('livewire.menu-sidebar');
    }
}
