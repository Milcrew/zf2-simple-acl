<?php
namespace Zf2SimpleAcl;

return array(
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
                'paths' => array(__DIR__ . '/../src/Zf2SimpleAcl/Entities')
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Zf2SimpleAcl\Entities' => 'front_driver'
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
