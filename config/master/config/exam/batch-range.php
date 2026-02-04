<?php

return [

    'title' => 'Batch Range',

    /**
     * 🔥 Status is now per ATTACHED COLLEGE
     */
    'status_field' => 'attached_college_status',

    'columns' => [
        // -------------------------------------------------
        // Stream
        // -------------------------------------------------
        'stream_name' => [
            'label'      => 'Stream',
            'type'       => 'text',
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'order'      => 1,
        ],

        // -------------------------------------------------
        // Exam Day
        // -------------------------------------------------
        'mas_batch_range_batch_name' => [
            'label'      => 'Batch',
            'type'       => 'text',
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'order'      => 2,
        ],

        // -------------------------------------------------
        // Exam Centre (Main)
        // -------------------------------------------------
        'exam_center_name' => [
            'label'      => 'Exam Centre',
            'type'       => 'text',
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'order'      => 3,
        ],

        // -------------------------------------------------
        // 🔥 Attached College (ONE PER ROW)
        // -------------------------------------------------
        'attached_college_name' => [
            'label'      => 'Attached College',
            'type'       => 'text',
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'order'      => 4,
        ],

        // -------------------------------------------------
        // 🔥 Students FROM THAT COLLEGE
        // -------------------------------------------------
        'attached_college_students' => [
            'label'      => 'Students',
            'type'       => 'number',
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'order'      => 5,
        ],

        // -------------------------------------------------
        // Dates
        // -------------------------------------------------
        'mas_batch_range_from_date' => [
            'label'      => 'Start Date',
            'type'       => 'date',
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'order'      => 6,
        ],

        'mas_batch_range_to_date' => [
            'label'      => 'End Date',
            'type'       => 'date',
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'order'      => 7,
        ],

        // -------------------------------------------------
        // 🔥 Status PER ATTACHED COLLEGE
        // -------------------------------------------------
        'mas_batch_range_status_id' => [
            'label'      => 'Status',
            'type'       => 'enum',
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'order'      => 8,
            'options' => [
                1 => 'Active',
                2 => 'In Active',
            ],
        ],
    ],
];
