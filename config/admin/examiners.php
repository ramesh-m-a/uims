<?php

return [

    'title' => 'Examiners',

    'status_field' => null,

    'columns' => [

        'photo_path' => [
            'label' => 'Photo',
            'type'  => 'text',
            'visible' => true,
            'order' => 1,
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

        'email' => [
            'label'      => 'Email',
            'sortable'   => true,
            'filterable' => true,
            'visible'    => true,
            'order'      => 9,
        ],

        'basicDetails.examinerDetails.examiner_details_type' => [
            'label'   => 'Type',
            'type'    => 'enum',
            'visible' => true,
            'order'   => 10,
            'options' => [
                1 => 'Internal',
                2 => 'External-O',
                3 => 'External',
            ],
        ],

        'action' => [
            'label'   => 'Action',
            'visible' => true,
            'order'   => 11,
        ],
    ],
];
