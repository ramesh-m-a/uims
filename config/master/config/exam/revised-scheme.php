<?php

return [

    'status_field' => 'mas_revised_scheme_status_id',

    'columns' => [

        // ✅ STREAM COLUMN (ADD THIS AT TOP)
        'stream.mas_stream_name' => [
            'label'      => 'Stream',
            'sortable'   => false,
            'filterable' => false,
            'visible'    => true,
            'order'      => 0,
        ],

        'mas_revised_scheme_name' => [
            'label'      => 'Name',
            'type'       => 'text',
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'order'      => 1,
        ],

        'mas_revised_scheme_short_name' => [
            'label'      => 'Short Name',
            'type'       => 'code',
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'order'      => 2,
        ],

        'mas_revised_scheme_status_id' => [
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
