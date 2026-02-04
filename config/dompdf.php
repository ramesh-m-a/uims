<?php

return [

    'enable_php' => true,
    'enable_css_float' => true,

    'options' => [
        'isPhpEnabled' => true,
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,

        // 🔥 THIS IS THE MISSING KEY
        'chroot' => [
            realpath(public_path()),
            realpath(storage_path()),
        ],
    ],

];
