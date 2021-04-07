<?php

namespace OapDatamng;

use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'datamng' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/datamng[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'datamng__index' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/datamng/index/index',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'datamng__upload' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/datamng/upload',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'upload',
                    ],
                ],
            ],
            'datamng__getUploadedFile' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/datamng/upload/getUploadedFile',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'getUploadedFile',
                    ],
                ],
            ],
            'datamng__confirmupload' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/datamng/upload/confirmupload',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'confirmupload',
                    ],
                ],
            ],
            
            

            //# Menu
            'menuoap' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/datamng/index',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],

    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            
        ],
    ],

    'access_filter' => [
        'options' => [
            'mode' => 'restrictive'
        ],
        'controllers' => [
            Controller\IndexController::class => [
                ['actions' => [], 'allow' => '*'],
                ['actions' => ['index', 'categorie', 'prodotto'], 'allow' => '@']
            ],
        ]
    ],
    'service_manager' => [
        'factories' => [
            Service\NavManager::class => Service\Factory\NavManagerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'oap' => __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'view_helper_config' => [
        'flashmessenger' => [
            'message_open_format'      => '<div%s><ul><li>',
            'message_close_string'     => '</li></ul></div>',
            'message_separator_string' => '</li><li>'
        ]
    ],
];