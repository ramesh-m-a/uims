<?php

return [
    'status_field' => 'mas_department_status_id',

    'columns' => [

        // STREAM NAME
        'stream.mas_stream_name' => [
            'label'      => 'Stream',
            'sortable'   => false,
            'filterable' => false,
            'order'      => 1,
        ],

        'mas_department_name' => [
            'label'      => 'department',
            'type'       => 'text',
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'order'      => 2,
        ],

        'mas_department_status_id' => [
            'label'      => 'Status',
            'type'       => 'enum',
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'order'      => 3,
            'options'    => [
                1 => 'Active',
                2 => 'In Active',
            ],
        ],
    ],
];
