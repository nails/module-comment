<?php

use Nails\Comment\Exception;
use Nails\Comment\Model;
use Nails\Comment\Resource;
use Nails\Comment\Service;

return [
    'services'  => [
        'Comment' => function (): Service\Comment {
            if (class_exists('\App\Comment\Service\Comment')) {
                return new \App\Comment\Service\Comment();
            } else {
                throw new Exception\Service\AppServiceRequiredException(
                    'The app must extend the Comment service'
                );
            }
        },
    ],
    'models'    => [
        'Comment'     => function (): Model\Comment {
            if (class_exists('\App\Comment\Model\Comment')) {
                return new \App\Comment\Model\Comment();
            } else {
                return new Model\Comment();
            }
        },
        'CommentFlag' => function (): Model\Comment\Flag {
            if (class_exists('\App\Comment\Model\Comment\Flag')) {
                return new \App\Comment\Model\Comment\Flag();
            } else {
                return new Model\Comment\Flag();
            }
        },
        'CommentVote' => function (): Model\Comment\Vote {
            if (class_exists('\App\Comment\Model\Comment\Vote')) {
                return new \App\Comment\Model\Comment\Vote();
            } else {
                return new Model\Comment\Vote();
            }
        },
    ],
    'resources' => [
        'Comment'     => function ($mObj): Resource\Comment {
            if (class_exists('\App\Comment\Resource\Comment')) {
                return new \App\Comment\Resource\Comment($mObj);
            } else {
                return new Resource\Comment($mObj);
            }
        },
        'CommentFlag' => function ($mObj): Resource\Comment\Flag {
            if (class_exists('\App\Comment\Resource\Comment\Flag')) {
                return new \App\Comment\Resource\Comment\Flag($mObj);
            } else {
                return new Resource\Comment\Flag($mObj);
            }
        },
        'CommentVote' => function ($mObj): Resource\Comment\Vote {
            if (class_exists('\App\Comment\Resource\Comment\Vote')) {
                return new \App\Comment\Resource\Comment\Vote($mObj);
            } else {
                return new Resource\Comment\Vote($mObj);
            }
        },
    ],
];
