<?php

return [
    'status_field' => 'mas_stream_status_id',

    'columns' => [
        'mas_stream_name' => [
            'label' => 'Stream',
            'type'  => 'text',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order' => 1,
        ],

        'mas_stream_short_code' => [
            'label' => 'Code',
            'type'  => 'code',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order' => 2,
        ],

        'mas_stream_status_id' => [
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
