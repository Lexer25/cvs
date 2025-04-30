
<!-- Static navbar -->
 <div class="navbar navbar-default navbar-fixed-top disable">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		  <?= HTML::anchor('dashboard', __('City'),  array('class'=>'navbar-brand')) ?>
    </div>
	<div class="navbar-collapse collapse">
        <ul class="nav navbar-nav">
            <? 
		if(Session::instance()->get('skud_number'))	
		{
			//echo Debug::vars('15', Kohana::$config->load('artonitcity_config')->view_without_auth['load'], Auth::instance()->logged_in()); exit;

			if((Kohana::$config->load('artonitcity_config')->view_without_auth['load']) OR (Auth::instance()->logged_in())) {?> <li <?if ($_SESSION['menu_active']=='load') echo 'class="active"';?> ><?= HTML::anchor('dashboard/load', __('Load')) ?></li><?};
						
			if((Kohana::$config->load('artonitcity_config')->view_without_auth['load_order']) OR (Auth::instance()->logged_in())) {?> <li <?if ($_SESSION['menu_active']=='load_order') echo 'class="active"';?> ><?= HTML::anchor('dashboard/load_order', __('Load_order')) ?></li><?};
			if((Kohana::$config->load('artonitcity_config')->view_without_auth['device_control']) OR (Auth::instance()->logged_in())) {?> <li <?if ($_SESSION['menu_active']=='device_control') echo 'class="active"';?> ><?= HTML::anchor('dashboard/device_control', __('device_control')) ?></li><?};

			//echo Debug::vars('20', $_SESSION); exit;
			if((Kohana::$config->load('artonitcity_config')->view_without_auth['events']) OR (Auth::instance()->logged_in())) {?> <li <?if (Arr::get($_SESSION,'menu_active')=='events') echo 'class="active"';?> ><?= HTML::anchor('event', __('events')) ?></li><?};
            if((Kohana::$config->load('artonitcity_config')->view_without_auth['people']) OR (Auth::instance()->logged_in())) {?> <li <?if (Arr::get($_SESSION,'menu_active')=='people') echo 'class="active"';?> ><?= HTML::anchor('people/peopleInfo', __('people')) ?></li><?};
            if((Kohana::$config->load('artonitcity_config')->view_without_auth['door']) OR (Auth::instance()->logged_in())) {?> <li <?if (Arr::get($_SESSION,'menu_active')=='door') echo 'class="active"';?> ><?= HTML::anchor('door/doorInfo', __('door')) ?></li><?};
            if((Kohana::$config->load('artonitcity_config')->view_without_auth['log']) OR (Auth::instance()->logged_in())) {?> <li <?if (Arr::get($_SESSION,'menu_active')=='log') echo 'class="active"';?> ><?= HTML::anchor('dashboard/log', __('log')) ?></li><?};
				?>
			<li><?= HTML::anchor('', __('__')) ?></li>
            <li <? //if (Arr::get($_SESSION,'menu_active')=='check') echo 'class="active"';?> ><?//= HTML::anchor('check', __('check')) ?></li>
            <li><?//= HTML::anchor('guide', __('guide')) ?></li>
            <li class="dropdown">
		          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo __('guide');?> <b class="caret"></b></a>
		          <ul class="dropdown-menu">
		            <li><?= HTML::anchor('guide', __('guide')) ?></li>
		            <!--
					<li><?= HTML::anchor('./', __('reserv')) ?></li>
		             <li class="divider"></li>
		            <li><?= HTML::anchor('guide', __('about')) ?></li>
					-->
		          </ul>
		   	</li>
			<li <?if (Arr::get($_SESSION,'menu_active')=='services') echo 'class="active"';?> ><? echo HTML::anchor('dashboard/services', __('services')); 
		};?></li>
			
			<li <?if (Arr::get($_SESSION,'menu_active')=='skud') echo 'class="active"';?> ><? echo  HTML::anchor('skud', __('сводная'));
			
				if(Session::instance()->get('skud_number'))	
				{?>
			</li>
			<?php include Kohana::find_file('views','apb', 'menu');?>
        </ul>
  
            
		<ul class="nav navbar-nav navbar-right">
			<li>
			<?
			//echo Debug::vars('5.05.2017 Пример подготовки пароля для 123', Auth::instance()->hash_password('123'));
					
			if(Auth::Instance()->logged_in())
			{
				echo 'Пользователь '.Auth::instance()->get_user();
					//echo Debug::vars('5.05.2017 Пример подготовки пароля для 123', Auth::instance()->hash_password('123'));
					echo '<div>'.HTML::anchor('logout', __('logout'), array('onclick' => 'return confirm(\'' . __('confirm.delete').'\')')).'</div>';
			} else {
			echo Form::open('dashboard', array('method' => 'post', 'class'=>'form-inline'));?>
				<div class="form-group">
					<label for="inputEmail" class="sr-only">Имя</label>
					<input type="text" class="form-control input-sm" id="inputEmail" placeholder="Имя" name="username">
					
				</div>
				<div class="form-group">	    
					<label for="inputPassword" class="sr-only">Пароль</label>
					<input type="password" class="form-control input-sm" id="inputPassword" placeholder="Пароль" name="password">
				</div>
				<div class="checkbox input-sm">
						<label><input type="checkbox" name="remember"> Запомнить</label>
				</div>
					<button type="submit" class="btn btn-primary input-sm">Войти</button>
			<?echo Form::close();
			}

		}?>
		</li>
		</ul>
						
    </div>
	<div class="navbar-collapse collapse">
      <?php 
	  
	  if(!is_null(Session::instance()->get('skud_number')))
	  {
      echo __('string_about', array(
      		'db'=> Arr::get(
      			Arr::get(
      					Kohana::$config->load('database')->fb,
      					'connection'
      					),
      		'dsn'),
      		'ver'=> Kohana::$config->load('artonitcity_config')->ver,
      		'developer'=> Kohana::$config->load('artonitcity_config')->developer,
      		)).'<br>';
			echo __('timerefresh', array ('tr'=> date("d.m.Y H:i",time())));
			//echo '<br>'.__('skud_number ').Session::instance()->get('skud_number');//.Debug::vars('92', $_SESSION);;
			//echo '<br>'.__('peopleEventsTimeFrom').Session::instance()->get('peopleEventsTimeFrom');
			//echo '<br>'.__('peopleEventsTimeTo').Session::instance()->get('peopleEventsTimeTo');
	  } else {
		  echo __('no_select_skud');
	  }
      echo Log::NOTICE;
	  ?>
	  
	  </div>
	  
</div>
