<?php defined('SYSPATH') OR die('No direct script access.');

return array(
    // Enable the API browser.  TRUE or FALSE
    'api_browser'  => false,

    // Enable these packages in the API browser.  TRUE for all packages, or a string of comma seperated packages, using 'None' for a class with no @package
    // Example: 'api_packages' => 'Kohana,Kohana/Database,Kohana/ORM,None',
    //'api_packages' => true,
    'api_packages' => 'Kohana/Database',

    // Enables Disqus comments on the API and User Guide pages
    //'show_comments' => Kohana::$environment === Kohana::PRODUCTION,

    // Leave this alone
    'modules' => array(

        // This should be the path to this modules userguide pages, without the 'guide/'. Ex: '/guide/modulename/' would be 'modulename'

        'kohana' => array(
            // Whether this modules userguide pages should be shown
            'enabled'     => false,
            // The name that should show up on the userguide index page
            'name'        => 'Kohana',
            // A short description of this module, shown on the index page
            'description' => 'Documentation for Kohana core/system.',
            // Copyright message, shown in the footer for this module
            'copyright'   => '&copy; 2008–2012 Kohana Team',
        ),

        'userguide' => array(
            'enabled'     => false,
            'name'        => 'Userguide',
            'description' => 'Documentation viewer and api generation.',
            'copyright'   => '&copy; 2008–2012 Kohana Team',
        ),

        'auth' => array(
            'enabled'     => false,
            'name'        => 'Auth',
            'description' => 'Official Kohana authentication module.',
            'copyright'   => '&copy; 2008–2012 Kohana Team',
        ),

        'cache' => array(
            'enabled'     => false,
            'name'        => 'Cache',
            'description' => 'Official Kohana cache management module.',
            'copyright'   => '&copy; 2008–2012 Kohana Team',
        ),

        'codebench' => array(
            'enabled'     => false,
            'name'        => 'Codebench',
            'description' => 'Official Kohana benchmarking module.',
            'copyright'   => '&copy; 2008–2012 Kohana Team',
        ),

        'database' => array(
            'enabled'     => false,
            'name'        => 'Database',
            'description' => 'Official Kohana database module.',
            'copyright'   => '&copy; 2008–2012 Kohana Team',
        ),

        'image' => array(
            'enabled'     => false,
            'name'        => 'Image',
            'description' => 'Official Kohana image manipulation module.',
            'copyright'   => '&copy; 2008–2012 Kohana Team',
        ),

        'minion' => array(
            'enabled'     => false,
            'name'        => 'Minion',
            'description' => 'Official Kohana CLI task module.',
            'copyright'   => '&copy; 2008–2012 Kohana Team',
        ),

        'orm' => array(
            'enabled'     => false,
            'name'        => 'ORM',
            'description' => 'Official Kohana object-relational mapping module.',
            'copyright'   => '&copy; 2008–2012 Kohana Team',
        ),
		'unittest' => array(
            'enabled'     => false,
            
        ),
		

    )
);