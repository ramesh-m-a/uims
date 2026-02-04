<?php

return [

    'status_field' => 'mas_examiner_scheme_distribution_status_id',

    'columns' => [

        'stream.mas_stream_name' => [
            'label'      => 'Stream',
            'sortable'   => false,
            'filterable' => false,
            'visible'    => true,
            'order'      => 0,
        ],

        'scheme.mas_revised_scheme_name' => [
            'label' => 'Scheme',
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'order' => 1,
        ],

        'mas_examiner_scheme_distribution_examiner_type_id' => [
            'label' => 'Examiner Type',
            'type'       => 'enum',
            'filterable' => true,
            'sortable'   => true,
            'visible'    => true,
            'order'      => 2,
            'options'    => [
                1 => 'Internal',
                2 => 'External',
            ],
        ],

        'mas_examiner_scheme_distribution_examiner_type_count' => [
            'label' => 'Count',
            'sortable' => true,
            'filterable' => false,
            'visible' => true,
            'order' => 3,
        ],

        'mas_examiner_scheme_distribution_status_id' => [
            'label' => 'Status',
            'type' => 'enum',
            'sortable' => true,
            'filterable' => true,
            'visible' => true,
            'order' => 4,
            'options' => [
                1 => 'Active',
                2 => 'In Active',
            ],
        ],

    ],

];
