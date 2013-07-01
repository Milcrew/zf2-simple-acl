<?php
use Zend\ServiceManager\ServiceManager;

return array(
        'invokables' => array(),
        'factories' => array(
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory'
        )
);