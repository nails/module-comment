<?php

return [
    'services' => [
        'Comment' => function () {
            if (class_exists('\App\Comment\Service\Comment')) {
                return new \App\Comment\Service\Comment();
            } else {
                return new \Nails\Comment\Service\Comment();
            }
        }
    ]
];
