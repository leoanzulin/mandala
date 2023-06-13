<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    //'basePath2' => '/var/www/edutec/sca',	
    'name' => 'Especialização em Educação e Tecnologias',
    'defaultController' => 'site/login',
    'theme' => 'custom',
    'preload' => array('log'),
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.components.exportador.*',
        'application.modules.rights.*',
        'application.modules.rights.components.*',
        'application.vendor.*',
    ),
    'language' => 'pt_br',
    'modules' => array(
        // uncomment the following to enable the Gii tool

//        'gii'=>array(
//          'class'=>'system.gii.GiiModule',
//          'password'=>'123456',
//          // If removed, Gii defaults to localhost only. Edit carefully to taste.
//          'ipFilters'=>array('127.0.0.1','::1'),
//          ),
        'rights' => array(
            'install' => false,
            'userClass' => 'Usuario',
            'userNameColumn' => 'nome',
            'userIdColumn' => 'cpf',
            'superuserName' => 'Admin',
        ),
    ),
    // application components
    'components' => array(
        'authManager' => array(
            'class' => 'RDbAuthManager',
            'defaultRoles' => array('Guest'), // Guest com letra maiúscula!
            // Problema com sensibilidade a caixa do banco
            // https://tahiryasin.wordpress.com/2013/03/29/solution-table-mydb-authassignment-doesnt-exist/#more-392
            'rightsTable' => 'rights',
            'assignmentTable' => 'authassignment',
            'itemTable' => 'authitem',
            'itemChildTable' => 'authitemchild',
        ),
        'user' => array(
            // enable cookie-based authentication
            'allowAutoLogin' => true,
            'class' => 'RWebUser',
        ),
        // uncomment the following to enable URLs in path-format

        /* 'urlManager'=>array(
          'urlFormat'=>'path',
          'rules'=>array(
          '<controller:\w+>/<id:\d+>'=>'<controller>/view',
          '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
          '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
          ),
          ), */

        'db' => array(
            'connectionString' => 'pgsql:host=192.168.200.1;port=5432;dbname=edutec',
            'emulatePrepare' => false,
            'username' => 'avaliacao',
            'password' => 'meqmeqmntdV!@#',
            'charset' => 'utf8',
        ),
        'dbAva' => array(
#            'connectionString' => 'pgsql:host=192.168.200.1;port=5432;dbname=ava',
#            migração AWS
            'connectionString' => 'pgsql:host=db-moodle-replica.aws.ufscar.br;port=5432;dbname=ava',
#
            'emulatePrepare' => false,
#            'username' => 'moodle',
#            'password' => 'vuusbdph#45',
#            migração AWS
            'username' => 'edutec',
            'password' => 'wFWT0Ts89xCDZTNB29q5',
#
            'charset' => 'utf8',
            'class' => 'CDbConnection',
        ),
        'errorHandler' => array(
            'errorAction' => 'site/error',
        ),
       'log' => array(
           'class' => 'CLogRouter',
           'routes' => array(
                array(
                   'class' => 'CFileLogRoute',
                   'levels' => 'error, warning',
                ),
                array(
                   'class' => 'ext.LogDb',
                   'autoCreateLogTable' => true,
                   'logTableName' => 'log',
                   'levels' => 'error, info',
                   'connectionID' => 'db',
                ),
                array(
                   'class' => 'CFileLogRoute',
                   'levels' => 'info',
                   'logFile' => 'informacoes.log',
                   'categories' => 'system.*',
                ),
                array(
                   'class' => 'CFileLogRoute',
                   'levels' => 'trace',
                   'logFile' => 'trace.log',
                ),
            ),
        ),
        'ePdf' => array(
            'class' => 'ext.yii-pdf.EYiiPdf',
            'params' => array(
                'mpdf' => array(
                    // Esse .mpdf.mpdf.* é importante
                    'librarySourcePath' => 'application.vendor.mpdf.mpdf.*',
                    'constants' => array(
                        '_MPDF_TEMP_PATH' => Yii::getPathOfAlias('application.runtime'),
                    ),
                    'class' => 'mpdf', // the literal class filename to be loaded from the vendors folder
                ),
            ),
        ),
    ),
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        // this is used in contact page
        'adminEmail' => 'webmaster@example.com',
    ),
);
