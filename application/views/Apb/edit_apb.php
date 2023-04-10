<? //http://itchief.ru/lessons/bootstrap-3/30-bootstrap-3-tables;
// страница отображения данных по парковочной системе
echo Form::open('apb/apb_control');
?>
<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title"><?echo Kohana::message('apb','apb_config').' '. Arr::get($apb_getinfo, 'NAME');
		echo Form::hidden('id_apb', $id_apb);?></h3>
	</div>
	<div class="panel-body">
		<?php 
		//echo Debug::vars('11', $apb_getinfo, Arr::get($apb_getinfo, 'ENABLED'));
		echo Form::hidden('id_apb', Arr::get($apb_getinfo, 'ID'));
		echo Kohana::message('apb','apb_name').Form::input('name', Arr::get($apb_getinfo, 'NAME'), array('maxlength'=>50)).'<br>';
		echo Kohana::message('apb','apb_duration').Form::input('duration', Arr::get($apb_getinfo, 'DURATION')).'<br>';
		$checked=(Arr::get($apb_getinfo, 'ENABLED') == 1)? 'TRUE' : 'FALSE';
		echo Kohana::message('apb','apb_enabled').Form::checkbox('is_active', NULL, (bool) $checked ).'<br>';
		echo Form::button('todo', Kohana::message('apb','apb_change_config'), array('value'=>'change_config','class'=>'btn btn-success', 'type' => 'submit'));	
		
		;?>
	</div> 
</div>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title"><?echo Kohana::message('apb','apb_door')?></h3>
	</div>
	<div class="panel-body">
		<?php
			echo Kohana::message('apb','apb');
			
			
		?>
<table class="table table-striped table-hover table-condensed">


		<tr>
			<th><?echo Kohana::message('apb','id_dev');?></th>
			<th><?echo Kohana::message('apb','enter');?></th>
			<th><?echo Kohana::message('apb','to_move');?></th>
			<th><?echo Kohana::message('apb','exit');?></th>
			<th><?echo Kohana::message('apb','delay_pass');?></th>
			
			
			
			
		</tr>
		<?php 
		foreach($apb_device_list as $key=>$value)
		{
			
			if(Arr::get($value,'IS_ENTER') == 1)// если это вход, то пишем слева
			{
			echo '<tr>';
				echo '<td>'.Arr::get($value,'ID_DEV').'</td>';
				echo '<td>'.Arr::get($value,'NAME').'</td>';
				echo '<td>'.Form::radio('id_dev_for_change',Arr::get($value,'ID_DEV')).'</td>';
				echo '<td>'.' '.'</td>';
				echo '<td>'.Form::input('delay_pass['.Arr::get($value,'ID_DEV').']', Arr::get($value,'DELAY')).'</td>';
				
				
			echo '</tr>';	
			}
			
			if(Arr::get($value,'IS_ENTER') == 0)// а если это выход, то пишем справа
			{
			echo '<tr>';
				echo '<td>'.Arr::get($value,'ID_DEV').'</td>';
				echo '<td>'.' '.'</td>';
				echo '<td>'.Form::radio('id_dev_for_change', Arr::get($value,'ID_DEV')).'</td>';
				echo '<td>'.Arr::get($value,'NAME').'</td>';
				echo '<td>'.Form::input('delay_pass['.Arr::get($value,'ID_DEV').']', Arr::get($value,'DELAY')).'</td>';
			echo '</tr>';	
			}
			
		}
		
			echo '<tr>';
				echo '<td>'.Form::button('todo', Kohana::message('apb','apb_change_EE','Поменять'), array('value'=>'change_door','class'=>'btn btn-success', 'type' => 'submit')).'</td>';
				echo '<td>'.Form::button('todo', Kohana::message('apb','apd_del_from_gate', 'Удалить'), array('value'=>'delete_door','class'=>'btn btn-success', 'type' => 'submit')).'</td>';
				echo '<td> </td>';
				echo '<td> </td>';
				echo '<td>'.Form::button('todo', Kohana::message('apb','apd_changer_pass_delay', 'Изменить'), array('value'=>'apd_changer_pass_delay','class'=>'btn btn-success', 'type' => 'submit')).'</td>';
			echo '</tr>';
		?>

		
		
	
	</table>		
			
	
	</div> 

</div>



<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title"><?echo Kohana::message('apb','apb_select_door')?></h3>
	</div>
	<div class="panel-body">
		<?php echo Kohana::message('apb','Kp_panel_inside');
		//echo Debug::vars('79', $door_list); exit;
		//echo Form::select('id_dev', $door_list, array('multiple'=>'"multiple"'));
		echo Form::select('id_dev', $door_list);
		echo Form::button('todo', Kohana::message('apb','apb_door_add_as_enter','Добавить как вход'), array('value'=>'add_door_enter','class'=>'btn btn-success', 'type' => 'submit'));
		echo Form::button('todo', Kohana::message('apb','apb_add_door_as_exit','Добавить как выход'), array('value'=>'add_door_exit','class'=>'btn btn-success', 'type' => 'submit'));
		?>
	</div> 
</div>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title"><?echo Kohana::message('apb','apb_view_inside')?></h3>
	</div>
	<div class="panel-body">
		<?php echo Kohana::message('apb','Kp_panel_inside2');?>

			<table class="table table-striped table-hover table-condensed">


		<tr>
			<th><?echo Kohana::message('apb','pp');?></th>
			<th><?echo Kohana::message('apb','apb_people_name', '117');?></th>
			<th><?echo Kohana::message('apb','enter_time', 'enter_time');?></th>
			<th><?echo Kohana::message('apb','exit_time', 'exit_time');?></th>

			
			
			
			
		</tr>
		<?php 
		//cho Debug::vars('126', $people_list_inside);
		foreach($people_list_inside as $key=>$value)
		{
			{
			echo '<tr>';
				echo '<td>'.Arr::get($value,'ID_DEV', 0).'</td>';
				echo '<td>'.HTML::anchor('/people/peopleInfo/'.Arr::get($value, 'ID_PEP'), Arr::get($value,'SURNAME').' '.Arr::get($value,'NAME').' '.Arr::get($value,'PATRONYMIC').' ('.(Arr::get($value,'ID_PEP')) ).')</td>';
				echo '<td>'.Arr::get($value,'ENTER_TIME', '--').'</td>';
				echo '<td>'.Arr::get($value,'EXIT_TIME', '--').'</td>';
				
			echo '</tr>';	
			}
			
			
		
			
		}
		?>

		
		
	
	</table>
		
		<?php
		echo Form::button('todo', Kohana::message('apb','apb_del_parking_inside','Очистить таблицу'), array('value'=>'clear_parking_inside','class'=>'btn btn-success', 'type' => 'submit'));
		
		?>
	</div> 
</div>

<?echo Form::close();?>	