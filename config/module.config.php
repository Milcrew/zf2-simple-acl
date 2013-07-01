<?php
namespace Acl;

return array(
    'service_manager' => include __DIR__ . '/module/service_manager.config.php',

    'translator' => array(
        'locale' => 'ru_RU',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo'
            )
        )
    ),

    'doctrine' => array(
        'driver' => array(
            'front_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/Acl/Entities')
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Acl\Entities' => 'front_driver'
                )
            )
        )
    ),

    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'XHTML1_TRANSITIONAL',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/exception',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/main.phtml',
            'error/403'               => __DIR__ . '/../view/error/403.phtml'
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        )
    )
);
