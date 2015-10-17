<?php

return array(
    'services' => array(
        'Comment' => function () {
            if (class_exists('\App\Comment\Library\Comment')) {
                return new \App\Comment\Library\Comment();
            } else {
                return new \Nails\Comment\Library\Comment();
            }
        }
    )
);
