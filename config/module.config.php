<?php
namespace Zf2SimpleAcl;

return array(
// Sample config with one route which allowed for role guest.
// and for all roles who based on this role.
//
//    'zf2simpleacl' => array(
//        'restrictions' => array(
//            'route/main' => array(\Zf2SimpleAcl\Entities\Role::GUEST => true)
//        )
//    ),

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
