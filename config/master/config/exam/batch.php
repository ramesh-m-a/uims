<?php

return [

    'title' => 'Batch',

    'status_field' => 'mas_batch_status_id',

    'columns' => [

        // Stream
        'stream.mas_stream_name' => [
            'label'      => 'Stream',
            'sortable'   => true,
            'filterable' => true,
            'visible' => true,
            'type'  => 'text',
            'order'      => 1,
        ],

        // Year / Month (computed via join or accessor)
        'year.mas_year_year' => [
            'label'      => 'Year',
            'sortable'   => true,
            'filterable' => true,
            'order'      => 2,
        ],

        'month.mas_month_name' => [
            'label'      => 'Month',
            'sortable'   => true,
            'filterable' => true,
            'order'      => 3,
        ],

        // Scheme
        'scheme.mas_revised_scheme_short_name' => [
            'label'      => 'Scheme',
            'sortable'   => true,
            'filterable' => true,
            'type'  => 'text',
            'order'      => 4,
        ],

        // Subject
        'subject.mas_subject_name' => [
            'label'      => 'Subject',
            'sortable'   => true,
            'filterable' => true,
            'type'  => 'text',
            'order'      => 5,
        ],

        // Exam Start Date
        'mas_batch_start_date' => [
            'label'      => 'Exam Start Date',
            'sortable'   => true,
            'filterable' => false,
            'type'       => 'date',
            'order'      => 6,
        ],

        // Centre (main)
        'centre.mas_college_name' => [
            'label'      => 'Exam Centre',
            'sortable'   => true,
            'filterable' => true,
            'type'  => 'text',
            'order'      => 7,
        ],

        // Attached Colleges + Counts (computed accessor)
        'mas_batch_attached_centre_id' => [
            'label'      => 'Attached Colleges',
            'sortable'   => false,
            'filterable' => false,
            'type'  => 'text',
            'order'      => 7.5,
        ],

        // Total Students
        'mas_batch_total_students' => [
            'label'      => 'Total Students',
            'sortable'   => true,
            'filterable' => false,
            'order'      => 8,
        ],

        // Total Groups
        'mas_batch_total_batches' => [
            'label'      => 'Groups',
            'sortable'   => true,
            'filterable' => false,
            'order'      => 9,
        ],

        // Status
        'mas_batch_status_id' => [
            'label'      => 'Status',
            'type'       => 'enum',
            'filterable' => true,
            'sortable'   => true,
            'order'      => 10,
            'options' => [
                1 => 'Active',
                2 => 'In Active',
            ],
        ],
    ],
];
