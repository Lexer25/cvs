<script type="text/javascript">
      $(function () {
		var dateBegin = new Date();
		dateBegin.setHours(22, 0, 0, 0);
		dateBegin.setMonth(dateBegin.getMonth()+2);
	    //Инициализация datetimepicker1
        $("#datetimepicker1").datetimepicker(
		{language: 'ru', 
		showToday: true,
		sideBySide: true,
		defaultDate: dateBegin
		}
		);
      });

      $(document).ready(function() {
    	    $("#check_all3").click(function () {
    	         if (!$("#check_all3").is(":checked"))
    	            $(".checkbox").prop("checked",false);
    	        else
    	            $(".checkbox").prop("checked",true);
    	    });
    	});

 
  	$(function() {		
  		$("#tablesorter").tablesorter({sortList:[[0,0]], headers: { 0:{sorter: false}}});
  	});	
	
</script> 
<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title"><?echo __('device_panel_title').' '.date('Y-m-d H:i:s')?></h3>
  </div>
  

  
  <div class="panel-body">
  
     <div class="panel panel-danger">

  <div class="panel-body">
    <?php echo __('device_panel_title_desc', array('date_from'=>$date_stat['min'], 'date_to'=>$date_stat['max']));?>
  </div>
  </div>
  
  <?echo Form::open('Dashboard/load');
  $brows=Arr::get($_SESSION, 'brows', 'full');
		?>
		<!-- 
 	<input type="radio" name="browser" value = "full" <?if($brows=='full') echo 'checked="checked"';?>> <?echo __('Show_all');?>
	<input type="radio" name="browser" value = "error" <?if($brows=='error') echo 'checked="checked"';?>> <?echo __('Show_error_only');?>
	 -->
	<button type="submit" class="btn btn-primary" name="refresh"  value="1"><?echo __('refresh')?></button>
<?echo Form::close();	

echo __('load_table', array('count_door'=>count($list)));

echo Form::open('Dashboard/device_control');?>	

   <!-- <table class="table table-striped table-hover table-condensed">  -->
   <table id="tablesorter" class="table table-striped table-hover table-condensed tablesorter">
   <thead allign="center">

		
		<tr>
			<th>
				Выделить<br><label><input type="checkbox" name="id_dev" id="check_all3"></label>
			</th>
			<?php
			echo '<th>'.__('SERVER_NAME').'</th>'; //2
			//echo '<th>'.__('SERVER_IP').'</th>'; //3
			//echo '<th>'.__('SERVER_PORT').'</th>'; //4
			echo '<th>'.__('DEVICE_TYPE').'</th>'; //4
			echo '<th>'.__('DEVICE_NAME').'</th>'; //5
			echo '<th>'.__('DOOR_NAME').'</th>'; //6
			//echo '<th>'.__('BASE_COUNT').'</th>'; //7
			echo '<th>'.__('DEVICE_VERSION').'</th>'; //8
			echo '<th>'.__('DEVICE_COUNT').'</th>'; //9
			echo '<th>'.__('delta_count').'</th>'; //90
			echo '<th>'.__('single_list').'</th>'; //91
			echo '<th>'.__('TEST_MODE').'</th>'; 10
			?>
			
		</tr>
		
		<tr align="center">
		<?php
			echo '<td>1</td>';
			echo '<td>2</td>';
			//echo '<td>3</td>';
			//echo '<td>4</td>';
			echo '<td>5</td>';
			echo '<td>6</td>';
			//echo '<td>7</td>';
			echo '<td>8</td>';
			echo '<td>9</td>';
			echo '<td>90</td>';
			echo '<td>91</td>';
			echo '<td>10</td>';
		?>
			
		</tr>
		
		</thead>
		<tbody>
		<? //echo Debug::vars('26', $_SESSION, $list);
		foreach ($list as $key => $value)
		{
			//echo Debug::vars('73', $value); exit;
			if($brows=='error' and Arr::get($value, 'TR_COLOR') !='success')
			{
			echo '<tr class="'.Arr::get($value, 'TR_COLOR', 'active').'">';
			echo '<td><label>'.Form::checkbox('id_dev['.Arr::get($value, 'ID_DOOR').']', Arr::get($value, 'ID_DOOR', 0), FALSE, array('class'=>'checkbox')).'</label></td>'; //1
				echo '<td>'.Arr::get($value, 'SERVER_NAME', 'No data').'</td>'; //2
				//echo '<td>'.Arr::get($value, 'SERVER_IP', 'No data').'</td>'; //3
				//echo '<td>'.Arr::get($value, 'SERVER_PORT', 'No data').'</td>'; //4
				echo '<td>'.Arr::get($value, 'DEVICE_VERSION', 'No data').'</td>'; //5
				echo '<td>'.Arr::get($value, 'DOOR_NAME', 'No data').'</td>'; //6
				//echo '<td>'.HTML::anchor('', Arr::get($value, 'BASE_COUNT', 'No data'), array('title'=> Arr::get($value, 'BASE_COUNT_READ', 'No data') )).'</td>'; //7
				echo '<td>'.Arr::get($value, 'DEVICE_NAME', 'No data').'</td>'; //8
				echo '<td>'
				.HTML::anchor('', Arr::get($value, 'BASE_COUNT_AT_TIME', 'No data'), array('title'=> Arr::get($value, 'BASE_COUNT_AT_TIME', 'No data') ))
				.' / '
				.HTML::anchor('', Arr::get($value, 'DEVICE_COUNT', 'No data'), array('title'=> Arr::get($value, 'TIME_INSERT', 'No data') ))
				
				.'</td>'; //9
				echo '<td>'.Arr::get($value, 'TEST_MODE', 'No data').'TEST_MODE111</td>';
				
			echo '</tr>';
			};
			if($brows=='full')
			{
			// формирование данных для выдеделния неактивных дверей. Дверь считается неактивной, если у контроллера есть метка Единый список и door=1. В этом случае строка должна быть серой, выбор заперещен
			$tr_class=Arr::get($value, 'TR_COLOR', 'active');
			if ((Arr::get($value, 'DB_COMMON_LIST') == 1) and ( Arr::get($value, 'ID_READER') == 1 ))// если в БД установлена метка Единый список и door=0, то это точкой прохода управлять нельзя
			{	
				$tr_class = 'active';
				$str_select = '<u title="'.__('title_not_select_in_load_table').'">'.__('off_rus').'</u>';
						
			} else {
						$delta=(Arr::get($value, 'BASE_COUNT_AT_TIME') - Arr::get($value, 'DEVICE_COUNT'));
						$str_select= Form::checkbox('id_dev['.Arr::get($value, 'ID_DOOR').']', Arr::get($value, 'ID_DOOR'), FALSE, array('class'=>'checkbox'));
					};
			
			echo '<tr class="'.$tr_class.'">';
				echo '<td>'.$str_select.'</td>';// 1
				echo '<td>'.__('title_server', array(
					'SERVER_IP'=>Arr::get($value, 'SERVER_IP', 'No data'),
					'SERVER_PORT'=>Arr::get($value, 'SERVER_PORT', 'No data'),
					'SERVER_NAME'=>Arr::get($value, 'SERVER_NAME', 'No data')
					)).'</td>'; //2
				echo '<td>'.
					HTML::anchor('device/deviceInfo/'. Arr::get($value, 'DEVICE_ID', 'no'),
					Arr::get($devtypeList,Arr::get($value, 'ID_DEVTYPE')).
					' ('.Arr::get($value, 'ID_DEVTYPE', 'no').')'
					).'</td>'; //5
				echo '<td>'.HTML::anchor('device/deviceInfo/'. Arr::get($value, 'DEVICE_ID'), Arr::get($value, 'DEVICE_NAME', 'No data')).'</td>'; //5
				echo '<td>'.HTML::anchor('door/doorInfo/'.Arr::get($value, 'ID_DOOR'), Arr::get($value, 'DOOR_NAME', 'No data')).'</td>'; //6
				//echo '<td>'.HTML::anchor('', Arr::get($value, 'BASE_COUNT', 'No data'), array('title'=> Arr::get($value, 'BASE_COUNT_READ', 'No data') )).'</td>'; //7
				echo '<td>'.Arr::get($value, 'DEVICE_VERSION', 'No data').'</td>';
				echo '<td>'.__('count_for_laod_table',
					array('BASE_COUNT_AT_TIME'=>Arr::get($value, 'BASE_COUNT_AT_TIME', 'No data'),
					'DEVICE_COUNT'=>Arr::get($value, 'DEVICE_COUNT', 'No data'),
					'KEYCOUNTTIME'=>Arr::get($value, 'KEYCOUNTTIME', 'No data'),
					'DBKEYCOUNTTIME'=>Arr::get($value, 'DBKEYCOUNTTIME', 'No data')
					)).'</td>';
				
				//90
				//$delta=0;
				if (is_numeric(Arr::get($value, 'BASE_COUNT_AT_TIME')) and is_numeric(Arr::get($value, 'DEVICE_COUNT') ))
				{					
					//$delta=(Arr::get($value, 'BASE_COUNT_AT_TIME') - Arr::get($value, 'DEVICE_COUNT'));
					//echo '<td>'. $delta.'<input type="hidden" name="id_dev[err_count_max]['.Arr::get($value, 'ID_DOOR').']" value="'.$delta.'"></td>';
					//echo  '<input type="hidden" name="id_dev[err_count_max]" value="'.$delta.'"';
					echo '<td>'.$delta.'</td>';
				} else {
					echo '<td>---</td>';
				}
			
				//91 вывод данных о едином списке
				$db_common_list = __('n/a');
				$read_common_list = __('n/a');
				if(Arr::get($value, 'DB_COMMON_LIST') == 1) $db_common_list = __('on_rus');
				if(Arr::get($value, 'DB_COMMON_LIST') == 0) $db_common_list = __('off_rus');
				if (Arr::get($value, 'READ_COMMON_LIST') == 1) $read_common_list = __('on_rus');
				if (Arr::get($value, 'READ_COMMON_LIST') == 0) $read_common_list = __('off_rus');
				$common_list=__('commont_list', array('DB_COMMON_LIST'=>$db_common_list,'READ_COMMON_LIST'=>$read_common_list ));
				$print_common_list = '<span class="label label-danger">'.$common_list.'</span>';//колонка 91
				if(Arr::get($value, 'DB_COMMON_LIST') == Arr::get($value, 'READ_COMMON_LIST')) $print_common_list = '<span class="label label-success">'.$common_list.'</span>';//колонка 91 
				echo '<td>'.$print_common_list.'</td>';
				
				
				$status_device = 'n/a';
				if(Arr::get($value, 'TEST_MODE') == 'TEST_OFF')$status_device = '<span class="label label-success">'.__('test_mode_is_off').'</span>';//колонка 10 
				if(Arr::get($value, 'TEST_MODE') == 'TEST_ON') $status_device =  '<span class="label label-danger">'.__('test_mode_is_on').'</span>';//колонка 10
				//10
				echo '<td>'.$status_device.'</td>';		
				
			echo '</tr>';
			
			}
			
		}
		?>
		</tbody>
	</table>

<nav class="navbar navbar-default navbar-fixed-bottom disable" role="navigation">
  <div class="container">
  	<button type="submit" class="btn btn-primary" name="synctime" value="1" title = "Синхронизация времени в контроллерах"><?echo __('synctime')?></button>
	<button type="submit" class="btn btn-primary" name="settz"  value="1" title = "Установить временные зоны для выбранных контроллеров"><?echo __('settz')?></button>
	<button type="submit" class="btn btn-danger" name="clear_device"  value="1" title = "Удалить карты из выбранных точек прохода"><?echo __('clear_device')?></button>
	<button type="submit" class="btn btn-danger" name="load_card"  value="1" title = "Загрузить карты в выбранные точки прохода"><?echo __('load_card')?></button>
	<!--<button type="submit" class="btn btn-info" name="checkStatusOnLine"  value="1" title = "Чтение текущего состояния контроллера он-лайн." disabled="disabled"><?echo __('checkStatusOnLine')?></button>-->
	<button type="submit" class="btn btn-success" name="checkStatus"  value="1" title = "Чтение состояния и запись данных в базу данных."><?echo __('checkStatus')?></button>
	<button type="submit" class="btn btn-warning" name="readkey"  value="1" title = "Вычитка карт из точки прохода и запись в файл"><?echo __('Comparekey')?></button>
	
	<?php 
		echo Form::button('control_door', 'Разблокировать', array('value'=>'unlockdoor','class'=>'btn btn-warning', 'type' => 'submit'));
		echo Form::button('control_door', 'Открыть 1 раз', array('value'=>'opendoor','class'=>'btn btn-warning', 'type' => 'submit'));
		echo Form::button('control_door', 'Открыть навсегда', array('value'=>'opendooralways','class'=>'btn btn-warning', 'type' => 'submit'));
		echo Form::button('control_door', 'Закрыть навсегда', array('value'=>'lockdoor','class'=>'btn btn-warning', 'type' => 'submit'));
	?>
	
	</div>
</nav>

<?echo Form::close();?>		
  </div>
</div>
