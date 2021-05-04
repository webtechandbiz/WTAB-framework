<?php

namespace OapDashboard;

use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'dashboard' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/dashboard[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'dashboard__index' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/dashboard/index/index',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'dashboard__saveform' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/dashboard/index/saveform',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'saveform',
                    ],
                ],
            ],

            //# Menu
            'menuoap' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/dashboard/index',
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
    //#TODO
    /*
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
    */
];
