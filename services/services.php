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
        'Comment'     => function ($resource, $model): Resource\Comment {
            if (class_exists('\App\Comment\Resource\Comment')) {
                return new \App\Comment\Resource\Comment($resource, $model);
            } else {
                return new Resource\Comment($resource, $model);
            }
        },
        'CommentFlag' => function ($resource, $model): Resource\Comment\Flag {
            if (class_exists('\App\Comment\Resource\Comment\Flag')) {
                return new \App\Comment\Resource\Comment\Flag($resource, $model);
            } else {
                return new Resource\Comment\Flag($resource, $model);
            }
        },
        'CommentVote' => function ($resource, $model): Resource\Comment\Vote {
            if (class_exists('\App\Comment\Resource\Comment\Vote')) {
                return new \App\Comment\Resource\Comment\Vote($resource, $model);
            } else {
                return new Resource\Comment\Vote($resource, $model);
            }
        },
    ],
];
