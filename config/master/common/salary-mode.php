<?php

return [
    'status_field' => 'mas_salary_mode_status_id',

    'columns' => [
        'mas_salary_mode_name' => [
            'label' => 'Name',
            'type'  => 'text',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order' => 1,
        ],

        'mas_salary_mode_status_id' => [
            'label' => 'Status',
            'type'  => 'enum',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order' => 2,
            'options' => [
                1 => 'Active',
                2  => 'In Active',
            ],
        ],
    ],
];
