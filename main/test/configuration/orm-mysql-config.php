<?php

return array(
    //Database connection settings, can be developed further to include multiple data sources.
    'connection' => array(
        'driver' => 'MySql',
        'hostname' => '',
        'username' => '',
        'password' => '',
        'database' => ''
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
