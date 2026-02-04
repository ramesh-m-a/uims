<?php

return [

    'title' => 'College',

    'status_field' => 'mas_college_status_id',

    'columns' => [

        // STREAM NAME (RELATION)
        'stream.mas_stream_name' => [
            'label'      => 'Stream',
            'sortable'   => true,
            'filterable' => true,
            'visible' => true,
            'type'  => 'text',
            'order'      => 1,
        ],

        'mas_college_name' => [
            'label'      => 'College Name',
            'sortable'   => true,
            'filterable' => true,
            'visible' => true,
            'type'  => 'text',
            'order'      => 2,
        ],

        'mas_college_code' => [
            'label'      => 'Code',
            'sortable'   => true,
            'filterable' => true,
            'visible' => true,
            'type'       => 'code',   // 👈 THIS IS THE TRIGGER
            'order'      => 3,
        ],

        'mas_college_exam_centre' => [
            'label' => 'Exam Centre',
            'type'  => 'enum',
            'options' => [
                1 => 'Yes',
                0 => 'No',
            ],
            'order' => 4,
        ],

        'mas_college_type' => [
            'label' => 'Type',
            'type'  => 'enum',
            'options' => [
                'G' => 'Government',
                'P' => 'Private',
            ],
            'order' => 5,
        ],

        'mas_college_is_internal' => [
            'label' => 'Internal',
            'type'  => 'enum',
            'options' => [
                1 => 'Yes',
                0 => 'No',
            ],
            'order' => 6,
        ],

        'mas_college_status_id' => [
            'label' => 'Status',
            'type'  => 'enum',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order' => 7,
            'options' => [
                1 => 'Active',
                2  => 'In Active',
            ],
        ],
    ],
];
