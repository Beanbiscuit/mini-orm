<?php

return array(
    //Database connection settings, can be developed further to include multiple data sources.
    'connection' => array(
        'driver' => 'MySql',
        'hostname' => '192.168.186.128',
        //'hostname' => 'localhost',
        'username' => 'admin',
        'password' => 'admin',
        'database' => 'my_helpdesk'
    ),
    //General ORM settings, logging etc.
    'orm' => array(
        'logfile' => '/tmp/miniorm.log'
    ),
    //Options below will be passed to the PHP data access object upon instantiation.
    'pdo' => array(
        'errormode' => 'ERRMODE_EXCEPTION'
    )
);
