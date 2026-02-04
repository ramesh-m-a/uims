<?php

return [

    'designation' => [
        'table' => 'mas_designation',
        'label' => 'designation',

        'columns' => [

            'mas_designation_name' => [
                'label'    => 'Designation',
                'type'     => 'string',
                'required' => true,
                'unique'   => true,
            ],

            'mas_designation_short_code' => [
                'label'    => 'Code',
                'type'     => 'string',
                'required' => false, // OPTIONAL
                'unique'   => true,
            ],

            'mas_designation_status_id' => [
                'label' => 'Status',
                'type'  => 'Status',
            ],
        ],
    ],

    // Add ANY master like this
];
