<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Сбор информации о системе
 *
 * @package  RESTfulAPI
 * @category Model
 * @author   Alon Pe'er
 */
class Model_Systems extends Model_RestAPI {

	public $name='mama';
	
	public function get($params)
	{
		
		return array(
			'restexample' => array(
				array('Version' => '1.0'),
				array('dsn' => 'dsn'),
		),
		);
	}

	

} // END