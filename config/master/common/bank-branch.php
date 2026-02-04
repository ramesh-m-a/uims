<?php

return [

    'status_field' => 'mas_bank_branch_status_id',

    'columns' => [

        'bank.mas_bank_name' => [
            'label' => 'Bank',
            'type'  => 'text',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order' => 1,
        ],

        'mas_bank_branch_branch_name' => [
            'label' => 'Branch',
            'type'  => 'text',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order' => 2,
        ],

        'mas_bank_branch_branch_city' => [
            'label' => 'City',
            'type'  => 'text',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order' => 3,
        ],

        'mas_bank_branch_status_id' => [
            'label'   => 'Status',
            'type'    => 'enum',
            'order'   => 4,
            'options' => [
                1 => 'Active',
                2 => 'In Active',
            ],
        ],
    ],
];
