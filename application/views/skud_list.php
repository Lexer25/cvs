<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="../../assets/ico/favicon.png">

    <title>Artonit City <?echo  isset(Kohana::$config->load('artonitcity_config')->city_name)? Kohana::$config->load('artonitcity_config')->city_name : '';?></title>

    <!-- Bootstrap core CSS -->
    <?= HTML::style('static/css/bootstrap.css'); ?>
	<?= HTML::style('static/css/modal.css'); ?>
    <?//= HTML::style('static/css/admin.css'); ?>
	<?//= HTML::style('static/css/timesheet.css'); ?>
	<?= HTML::style('static/css/city.css'); ?>
	<?//= HTML::style('static/css/modal.css'); ?>
	<link rel="stylesheet" href="/city/static/css/themes/blue/style.css" type="text/css" media="print, projection, screen" />
	 
<!-- ... -->
  <!-- 1. Подключить библиотеку jQuery -->
  <!-- <script type="text/javascript" src="/city/static/js/jquery-1.11.1.min.js"></script>  --> 
   <script type="text/javascript" src="/city/static/js/jquery-2.2.4.js"></script>
  
  <!-- 2. Подключить скрипт moment-with-locales.min.js для работы с датами -->
  <script type="text/javascript" src="/city/static/js/moment-with-locales.min.js"></script>
  <!-- 3. Подключить скрипт платформы Twitter Bootstrap 3 -->
  <script type="text/javascript" src="/city/static/js/bootstrap.min.js"></script>
  <!-- 4. Подключить скрипт виджета "Bootstrap datetimepicker" -->
  <script type="text/javascript" src="/city/static/js/bootstrap-datetimepicker.min.js"></script>
  <!-- 5. Подключить CSS платформы Twitter Bootstrap 3 -->  
  <link rel="stylesheet" href="/city/static/css/bootstrap.min.css" />
  <!-- 6. Подключить CSS виджета "Bootstrap datetimepicker" -->  
  <link rel="stylesheet" href="/city/static/css/bootstrap-datetimepicker.min.css" />
  
  
    

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="../../assets/js/html5shiv.js"></script>
      <script src="../../assets/js/respond.min.js"></script>
    <![endif]-->
    
    
   <!--  Скрипты для сортировки таблицы 
     <script type="text/javascript" src="/city/static/js/sort/jquery-latest.js"></script> --> 
	<script type="text/javascript" src="/city/static/js/sort/jquery.tablesorter.js"></script>
	 
  </head>
    <body>
		<div class="container">
				<!-- Static navbar -->
			 <div class="navbar navbar-default">
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
						<li><?php echo HTML::anchor('skud', __('сводная'));?></li>
					</ul>
				</div>
			</div>

			<div class="panel panel-primary">
			  <div class="panel-heading">
				<h3 class="panel-title"><?echo __('skud_list').' '.date('Y-m-d H:i:s')?></h3>
			  </div>
			  

			  
			  <div class="panel-body">
			  
				<div class="panel panel-danger">

					  <div class="panel-body">
						<?php 
							//echo Debug::vars('14', $skud_list); вывод списка переменных для отладки
							echo __('skud_list', array('method'=>'post'));
						
						?>
					  </div>
				</div>
				
				<table id="tablesorter" class="table table-striped table-hover table-condensed tablesorter">
			   <thead allign="center">

					
					<tr>
						<?php
						echo '<th>'.__('SER_NUM').'</th>'; //1
						echo '<th>'.__('name_skud').'</th>'; //1
						echo '<th>'.__('database_place').'</th>'; //2
						echo '<th>'.__('db_connect').'</th>'; //3
						echo '<th>'.__('countcar_check_connect').'</th>'; //3
						echo '<th>'.__('count_err_analyt').'</th>'; //4
						echo '<th>'.__('time_exec').'</th>'; //5
						//echo '<th>'.__('name').'</th>'; //5
						
						?>
						
					</tr>
					
					<tr align="center">
					<?php
						echo '<td>1</td>';
						echo '<td>2</td>';
						echo '<td>3</td>';
						echo '<td>4</td>';
						echo '<td>5</td>';
						echo '<td>6</td>';
						echo '<td>7</td>';
						//echo '<td>6</td>';
						
					?>
						
					</tr>
					
					</thead>
					<tbody>
					<?php
					echo Form::open('skud');
					$i=1;
					if(isset($skud_list))
					{
						foreach ($skud_list as $key=>$value)
						{
							echo '<tr>';
								//echo '<td>'.$i++.Form::radio('skud_number', $key, ($key == Session::instance()->get('skud_number'))? "checked" : "").'</td>';
								echo '<td>'.$key.' '.Form::radio('skud_number', $key, $key == Session::instance()->get('skud_number')).'</td>';
								echo '<td>'.Arr::get($value, 'name').'</td>';
								echo '<td>'.Arr::get(Arr::get(Arr::get($value, 'fb_connection'), 'connection'), 'dsn').'</td>';
								echo '<td>'.Arr::get($value, 'db_connect').'</td>';
								echo '<td>'.Arr::get($value, 'count_card', '---').'</td>';
								echo '<td>'.Arr::get($value, 'err_657', '---').'</td>';
								echo '<td>'.number_format(Arr::get($value, 'time_exec'), 3).'</td>';
								//echo '<td>'.Arr::get($value, 'nameSkud').'</td>';
							echo '</tr>';
						}
						
					} else {
						
						
						
					}
				
				?>
					</tbody>
				</table>
				
				<div class="btn-group">
											<? $dis='';
											//if(empty($device_list)) $dis='disabled="disabled"';
											echo ' <button type="submit" class="btn btn-primary" name="select_skud_button"  value="1" title = "" '.$dis.'>'. __('Выбрать объект').'</button>';
											?>
										</div>


			<?
				echo Form::close();?>		
				<p class="text-danger">
				<?
				echo __('phpversion').' '. phpversion().'<br>';
				echo __('Kohana:: VERSION').' '. Kohana:: VERSION.'<br>';
				echo __('Time_execute_page', array('time_exec' => number_format((microtime(1) - $time_exec_start), 3))).'<br>';?>
				</p>
			  </div>
		</div>
  </div>
			</div>
		
<?php
	
?>	
  </body>
</html>