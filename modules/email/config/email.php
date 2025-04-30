<?php
return array(
	/**
	 * Mailer type
	 *
	 * Valid drivers are: SwiftMailer, PHPMailer
	 */
  'mailer' => 'SwiftMailer',
	
	/**
	 * Email driver
	 *
	 * Valid drivers are: native, sendmail, smtp
	 */
	'driver' => 'smtp',
	
	/**
	 * Driver options
   *
	 * @param   null    native: no options
	 * @param   string  sendmail: executable path, with -bs or equivalent attached
	 * @param   array   smtp: hostname, (username), (password), (port), (encryption)
	 */
		 
	///'options' => NULL,
	 'options' => array('hostname' => 'mail.artonit.ru',
                                    'username' => 'support@artonit.ru',
                                    'password' => 'KkGDy7So15!J',
                                    'port'     => '25',
                                    'encryption' => '',
									),
	
  
	/**
	 * Force to: Send all email to
	 */
   'force_to' => NULL,

	/**
	 * Whitelist: array with emails of recipients which can receive mail
	 */
   'whitelist' => NULL
);