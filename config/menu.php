<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ROOT
    |--------------------------------------------------------------------------
    */

    'dashboard' => [
        'title'  => 'Home',
        'parent' => null,
        'route'  => 'dashboard',
        'icon'   => 'home',
    ],

    /*
    |--------------------------------------------------------------------------
    | MASTER (VIRTUAL ROOT)
    |--------------------------------------------------------------------------
    */

    'master' => [
        'title'  => 'Master',
        'parent' => 'dashboard',
        'route'  => null,
        'icon'   => 'layers',
    ],

    /*
    |--------------------------------------------------------------------------
    | CONFIG (VIRTUAL)
    |--------------------------------------------------------------------------
    */

    'master.config' => [
        'title'  => 'Config',
        'parent' => 'master',
        'route'  => null,
        'icon'   => 'settings',
    ],

    'master.common' => [
        'title'  => 'Common',
        'parent' => 'master',
        'route'  => null,
        'icon'   => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | CONFIG GROUPS (VIRTUAL)
    |--------------------------------------------------------------------------
    */

    'master.config.academic' => [
        'title'  => 'Academic',
        'parent' => 'master.config',
        'route'  => null,
        'icon'   => 'graduation-cap',
    ],

    'master.config.general' => [
        'title'  => 'General',
        'parent' => 'master.config',
        'route'  => null,
        'icon'   => 'sliders',
    ],

    /*
    |--------------------------------------------------------------------------
    | ACADEMIC MASTERS
    |--------------------------------------------------------------------------
    */

    'master.config.academic.stream.index' => [
        'title'  => 'Stream',
        'parent' => 'master.config.academic',
        'route'  => 'master.config.academic.stream.index',
        'icon'   => 'layers',
    ],

    'master.config.academic.college.index' => [
        'title'  => 'College',
        'parent' => 'master.config.academic',
        'route'  => 'master.config.academic.college.index',
        'icon'   => 'building',
    ],


    /*
    |--------------------------------------------------------------------------
    | COMMON MASTERS
    |--------------------------------------------------------------------------
    */

    'master.common.gender.index' => [
        'title'  => 'Gender',
        'parent' => 'master.common',
        'route'  => 'master.common.gender.index',
        'icon'   => 'users',
    ],

    'master.common.religion.index' => [
        'title'  => 'Religion',
        'parent' => 'master.common',
        'route'  => 'master.common.religion.index',
        'icon'   => 'bookmark',
    ],

    'master.common.nationality.index' => [
        'title'  => 'Nationality',
        'parent' => 'master.common',
        'route'  => 'master.common.nationality.index',
        'icon'   => 'globe',
    ],

    'master.common.status.index' => [
        'title'  => 'Status',
        'parent' => 'master.common',
        'route'  => 'master.common.status.index',
        'icon'   => 'globe',
    ],

    'master.common.state.index' => [
        'title'  => 'State',
        'parent' => 'master.common',
        'route'  => 'master.common.status.index',
        'icon'   => 'location-arrow',
    ],


    'master.common.district.index' => [
        'title'  => 'Status',
        'parent' => 'master.common',
        'route'  => 'master.common.status.index',
        'icon'   => 'location-arrow',
    ],

    'master.common.taluk.index' => [
        'title'  => 'Status',
        'parent' => 'master.common',
        'route'  => 'master.common.status.index',
        'icon'   => 'location-arrow',
    ],


    /*
   |--------------------------------------------------------------------------
   | ADMIN
   |--------------------------------------------------------------------------
   */

    'master.role.index' => [
        'title'  => 'Role',
        'parent' => 'master',
        'route'  => 'master.role.index',
        'icon'   => 'location-arrow',
    ],

];
