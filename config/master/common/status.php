<?php

return [
    'status_field' => 'is_active',

    'columns' => [
        'mas_status_name' => [
            'label' => 'Name',
            'type'  => 'text',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order' => 1,
        ],

        'is_active' => [
            'label' => 'Status',
            'type'  => 'enum',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order' => 3,
            'options' => [
                1 => 'Active',
                2  => 'In Active',
            ],
        ],
    ],
];
