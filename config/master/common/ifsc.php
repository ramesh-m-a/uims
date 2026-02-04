<?php

return [

    'status_field' => 'mas_ifsc_status_id',

    'columns' => [

        'mas_ifsc_number' => [
            'label' => 'IFS Code',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order' => 1,
        ],

        'bank.mas_bank_name' => [
            'label' => 'Bank',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order' => 2,
        ],

        'branch.mas_bank_branch_branch_name' => [
            'label' => 'Branch',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order' => 3,
        ],

        'mas_ifsc_status_id' => [
            'label'   => 'Status',
            'type'    => 'enum',
            'order'   => 4,
            'options' => [
                1 => 'Active',
                2 => 'Inactive',
            ],
        ],
    ],
];
