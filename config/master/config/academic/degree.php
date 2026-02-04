<?php

return [

    'status_field' => 'mas_degree_status_id',

    'columns' => [

        // DEGREE NAME (RELATION)
        'stream.mas_stream_name' => [
            'label'      => 'Stream',
            'sortable'   => true,
            'filterable' => true,
            'order'      => 1,
        ],

        'mas_degree_code' => [
            'label'      => 'Degree',
            'sortable'   => true,
            'filterable' => true,
            'order'      => 2,
        ],

        'mas_degree_name' => [
            'label'      => 'Name',
            'sortable'   => true,
            'filterable' => true,
            'order'      => 3,
        ],

        'level.mas_degree_level_name' => [
            'label'      => 'Level',
            'sortable'   => true,
            'filterable' => true,
            'order'      => 3,
        ],

        'mas_degree_specialisation_mode' => [
            'label'      => 'Specialization',
            'sortable'   => true,
            'filterable' => true,
            'order'      => 3,
        ],
        'mas_degree_status_id' => [
            'label' => 'Status',
            'type'  => 'enum',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order' => 4,
            'options' => [
                1 => 'Active',
                2  => 'In Active',
            ],
        ],
    ],
];
