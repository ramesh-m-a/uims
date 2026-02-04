<?php

return [

    'status_field' => 'mas_batch_split_status_id',

    'columns' => [

        // 🔥 FIXED: must match the alias returned from query
        'mas_batch_split_batch_name' => [
            'label'      => 'Name of the Batch',
            'type'       => 'text',
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'order'      => 1,
        ],

        'stream_name' => [
            'label'      => 'Stream',
            'type'       => 'text',
            'filterable' => true,
            'sortable'   => false,
            'visible'    => true,
            'order'      => 2,
        ],

        'exam_center_name' => [
            'label'      => 'Exam Centre',
            'type'       => 'text',
            'filterable' => true,
            'sortable'   => false,
            'visible'    => true,
            'order'      => 3,
        ],

        'attached_colleges' => [
            'label'      => 'Attached Colleges',
            'type'       => 'text',
            'filterable' => true,
            'sortable'   => false,
            'visible'    => true,
            'order'      => 4,
        ],

        'mas_batch_split_students' => [
            'label'      => 'Students',
            'type'       => 'number',
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'order'      => 5,
        ],

        'mas_batch_split_status_id' => [
            'label'      => 'Status',
            'type'       => 'enum',
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'order'      => 6,
            'options'    => [
                1 => 'Active',
                2 => 'In Active',
            ],
        ],
    ],
];
