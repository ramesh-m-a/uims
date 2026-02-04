<?php

return [
    'status_field' => 'mas_subject_status_id',

    'columns' => [

        'stream.mas_stream_name' => [
            'label' => 'Stream',
            'type'  => 'text',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order' => 1,
        ],

        'degree.mas_degree_name' => [
            'label' => 'Degree',
            'type'  => 'text',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order' => 2,
        ],

        'department.mas_department_name' => [
            'label' => 'Department',
            'type'  => 'text',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order' => 3,
        ],

        'mas_subject_name' => [
            'label' => 'Subject',
            'type'  => 'text',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order' => 4,
        ],

        'mas_subject_status_id' => [
            'label' => 'Status',
            'type'  => 'enum',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order' => 5,
            'options' => [
                1 => 'Active',
                2 => 'In Active',
            ],
        ],
    ],
];
