<?php

return [

    'roles' => [

        'admin' => [

            'kpis' => [
                'registered' => [
                    'label' => 'Registered Users',
                    'query' => 'users.count',
                    'permission' => 'dashboard.view.registered',
                    'route' => '/admin/users',
                    'cache' => 300,
                ],
                'approved' => [
                    'label' => 'Approved',
                    'query' => 'users.status_count',
                    'status' => 'approved',
                    'permission' => 'dashboard.view.approved',
                    'route' => '/admin/users?status=approved',
                    'cache' => 300,
                ],
                'rejected' => [
                    'label' => 'Rejected',
                    'query' => 'users.status_count',
                    'status' => 'rejected',
                ],
                'verification_pending' => [
                    'label' => 'Verification Pending',
                    'query' => 'users.status_count',
                    'status' => 'verification_pending',
                ],
            ],

            'queues' => [
                'stale_drafts' => [
                    'label' => 'Drafts > 7 days',
                    'query' => 'users.stale_drafts',
                    'days'  => 7,
                    'limit' => 10,
                    'permission' => 'dashboard.queue.drafts',
                    'route' => '/admin/users?status=draft',
                    'cache' => 120,
                ],
                'pending_verification' => [
                    'label' => 'Verification Pending > 48h',
                    'query' => 'users.pending_verification',
                    'hours' => 48,
                    'limit' => 10,
                ],
            ],

            'breakdowns' => [
                'stream' => [
                    'column' => 'user_stream_id',
                    'label'  => 'Stream Wise',
                    'chart'  => 'bar',
                    'permission' => 'dashboard.breakdown.stream',
                    'route' => '/admin/users?stream={partials}',
                    'cache' => 600,
                ],
                'designation' => [
                    'column' => 'user_designation_id',
                    'label'  => 'Designation Wise',
                    'chart'  => 'bar',
                ],
                'department' => [
                    'column' => 'basic_details.basic_details_department_id',
                    'label'  => 'Department Wise',
                    'chart'  => 'table',
                ],
            ],
        ],

        'college' => [
            'kpis' => [
                'teachers_total' => [
                    'label' => 'Total Teachers',
                    'query' => 'users.count',
                ],
                'approved' => [
                    'label' => 'Approved',
                    'query' => 'users.status_count',
                    'status' => 'approved',
                ],
            ],
            'queues' => [
                'pending_verification' => [
                    'label' => 'Pending Verification',
                    'query' => 'users.status_list',
                    'status' => 'verification_pending',
                    'limit' => 10,
                ],
            ],
            'breakdowns' => [
                'stream' => ['column' => 'user_stream_id', 'label' => 'Stream Wise'],
                'designation' => ['column' => 'user_designation_id', 'label' => 'Designation Wise'],
                'department' => ['column' => 'basic_details.basic_details_department_id', 'label' => 'Department Wise'],
            ],
        ],

        'principal' => [
            'kpis' => [
                'pending_reviews' => [
                    'label' => 'Pending Reviews',
                    'query' => 'drafts.status_count',
                    'status' => 'pending',
                ],
                'approved_by_me' => [
                    'label' => 'Approved by Me',
                    'query' => 'drafts.status_count',
                    'status' => 'approved',
                ],
            ],
            'queues' => [
                'review_queue' => [
                    'label' => 'Profiles Awaiting Review',
                    'query' => 'drafts.pending_list',
                    'limit' => 10,
                ],
            ],
            'breakdowns' => [
                'stream' => ['column' => 'u.user_stream_id', 'label' => 'Stream Load'],
                'designation' => ['column' => 'u.user_designation_id', 'label' => 'Designation Load'],
                'department' => ['column' => 'basic_details.basic_details_department_id', 'label' => 'Department Load'],
            ],
        ],
    ],
];
