<?php

echo __('Страница тестирования API');
if(isset($res)) 
{
	echo Debug::vars($res);
} else {
	echo __('Нет данных.');
}
echo Form::open('dashboardapi/control');
	
	echo Form::button('press_get', 'GET', array('value'=>'get','class'=>'btn btn-success', 'type' => 'submit')).'<br>';
	echo Form::button('press_get', 'POST', array('value'=>'post','class'=>'btn btn-success', 'type' => 'submit')).'<br>';
	echo Form::button('press_get', 'PUT', array('value'=>'put','class'=>'btn btn-success', 'type' => 'submit')).'<br>';
	echo Form::button('press_get', 'DELETE', array('value'=>'delete','class'=>'btn btn-success', 'type' => 'submit')).'<br>';
	
echo Form::close();

?>


