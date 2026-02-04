<?php

return [
    'status_field' => 'mas_designation_status_id',

    'columns' => [

        // STREAM NAME
        'stream.mas_stream_name' => [
            'label'      => 'Stream',
            'sortable'   => false,
            'filterable' => false,
            'order'      => 1,
        ],

        /*'streams' => [
            'label'      => 'Stream',
            'sortable'   => false,   // ✅ MUST be false for many-to-many
            'filterable' => false,   // ✅ same reason
            'order'      => 1,
        ],*/

        'mas_designation_name' => [
            'label'      => 'Designation',
            'type'       => 'text',
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'order'      => 2,
        ],

        'mas_designation_status_id' => [
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
