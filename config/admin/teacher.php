<?php

return [

    'title' => 'Teachers',

    'status_field' => null,

    'columns' => [

        'photo_path' => [
            'label' => 'Photo',
            'type'  => 'text',
            'visible' => true,
            'order' => 1,
        ],

        'stream.mas_stream_name' => [
            'label'      => 'Stream',
            'sortable'   => true,
            'filterable' => true,
            'visible'    => true,
            'order'      => 2,
        ],

        'college.mas_college_name' => [
            'label'      => 'College Name',
            'sortable'   => true,
            'filterable' => true,
            'visible'    => true,
            'order'      => 3,
        ],

        'name' => [
            'label'      => 'Teacher Name',
            'sortable'   => true,
            'filterable' => true,
            'visible'    => true,
            'order'      => 4,
        ],

        'designation.mas_designation_name' => [
            'label'      => 'Designation',
            'sortable'   => true,
            'filterable' => true,
            'visible'    => true,
            'order'      => 5,
        ],

        'basicDetails.department.mas_department_name' => [
            'label'      => 'Department',
            'sortable'   => true,
            'filterable' => true,
            'visible'    => true,
            'order'      => 6,
        ],

        'basicDetails.examinerDetails.examiner_details_rank' => [
            'label'      => 'Seniority',
            'sortable'   => true,
            'filterable' => true,
            'visible'    => true,
            'order'      => 7,
        ],

        'mobile' => [
            'label'      => 'Mobile',
            'sortable'   => true,
            'filterable' => true,
            'visible'    => true,
            'order'      => 8,
        ],

        'basicDetails.examinerDetails.examiner_details_type' => [
            'label'   => 'Type',
            'type'    => 'enum',
            'visible' => true,
            'order'   => 9,
            'options' => [
                1 => 'External',
                2 => 'Internal',
            ],
        ],

        'action' => [
            'label'   => 'Action',
            'visible' => true,
            'order'   => 10,
        ],
    ],
];
