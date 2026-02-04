<?php

return [
    'status_field' => 'mas_district_status_id',

    'columns' => [
        'mas_district_name' => [
            'label' => 'Name',
            'type'  => 'text',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order' => 1,
        ],

        'mas_district_status_id' => [
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
