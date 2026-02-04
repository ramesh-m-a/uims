<?php

return [

    'title' => 'User',

    'status_field' => 'user_status_id',

    'columns' => [

        // 📷 PHOTO (placeholder for future)
        'partials' => [
            'label'  => 'Photo',
            'type'   => 'text',
            'order'  => 1,
        ],

        'name' => [
            'label'    => 'Name',
            'type'     => 'text',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order'    => 2,
        ],

        'mobile' => [
            'label'    => 'Mobile',
            'type'     => 'text',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order'    => 3,
        ],

        'email' => [
            'label'    => 'Email',
            'type'     => 'text',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order'    => 4,
        ],

        'stream.mas_stream_name' => [
            'label' => 'Stream',
            'type'  => 'text',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order' => 5,
        ],

        'college.mas_college_name' => [
            'label' => 'College',
            'type'  => 'text',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order' => 6,
        ],

        'designation.mas_designation_name' => [
            'label' => 'Designation',
            'type'  => 'text',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order' => 7,
        ],

        'department.mas_department_name' => [
            'label' => 'Department',
            'type'  => 'text',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order' => 8,
        ],

        'role.name' => [
            'label' => 'Role',
            'type'  => 'text',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order' => 9,
        ],

        'user_status_id' => [
            'label'   => 'Status',
            'type'    => 'enum',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'options' => [
                1 => 'Active',
                2 => 'In Active',
            ],
            'order' => 10,
        ],
    ],
];
