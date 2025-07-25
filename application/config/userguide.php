<?php defined('SYSPATH') OR die('No direct script access.');

return array(
		// Enable the API browser.  TRUE or FALSE
		'api_browser'  => true,

	// Enable these packages in the API browser.  TRUE for all packages, or a string of comma seperated packages, using 'None' for a class with no @package
	// Example: 'api_packages' => 'Kohana,Kohana/Database,Kohana/ORM,None',
		//'api_packages' => true,
		'api_packages' => 'dashboard, Tools, cvtTest',
		//'api_packages' => 'None',

	// Enables Disqus comments on the API and User Guide pages
	//'show_comments' => Kohana::$environment === Kohana::PRODUCTION,
	
	
	// Leave this alone
	'modules' => array(

		// This should be the path to this modules userguide pages, without the 'guide/'. Ex: '/guide/modulename/' would be 'modulename'
		'cvs' => array(

			// Whether this modules userguide pages should be shown
				'enabled' => true,

			// The name that should show up on the userguide index page
			'name' => 'cvs',

			// A short description of this module, shown on the index page
			'description' => 'Парковочная система для жилых комплексов',

			// Copyright message, shown in the footer for this module
			'copyright' => '&copy; 2008–2025 Artsec',
		),
		
		'Auth' => array
        (
            'enabled' => FALSE,
        ),
		'kohana' => array(

			// Whether this modules userguide pages should be shown
				'enabled' => false,

			// The name that should show up on the userguide index page
			'name' => 'Kohana',

			// A short description of this module, shown on the index page
			'description' => 'Documentation for Kohana core/system.',

			// Copyright message, shown in the footer for this module
			'copyright' => '&copy; 2008–2012 Kohana Team',
		),
		
		'userguide' => array(

			// Whether this modules userguide pages should be shown
				'enabled' => false,
			
			// The name that should show up on the userguide index page
			'name' => 'Userguide',

			// A short description of this module, shown on the index page
			'description' => 'Documentation viewer and api generation.',
			
			// Copyright message, shown in the footer for this module
			'copyright' => '&copy; 2008–2012 Kohana Team',
		),
		
		'image' => array(

			'enabled' => false,
		)	,
		
		'minion' => array(

			'enabled' => false,
		),	
		'database' => array(

			'enabled' => false,
		),	
		'auth' => array(

			'enabled' => false,
		),	
		'arr' => array(

			'enabled' => false,
		),	
		
	)
);