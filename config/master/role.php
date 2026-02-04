<?php

return [

    'title' => 'Role Titleeee role php',

    'status_field' => 'roles_status_id',

    'columns' => [

        'name' => [
            'label'    => 'Role Name',
            'type'  => 'text',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order'    => 1,
        ],

        'description' => [
            'label'    => 'Description',
            'type'  => 'text',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'order'    => 2,
        ],

        'roles_status_id' => [
            'label' => 'Status',
            'type'  => 'enum',
            'filterable' => true,
            'sortable' => true,
            'visible' => true,
            'options' => [
                1 => 'Active',
                2 => 'In Active',
            ],
            'order' => 3,
        ],
    ],
];
