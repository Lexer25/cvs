<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title"><?echo __('event_panel_title', array('count_event'=>count($event_stat)))?></h3>
  </div>
  <div class="panel-body">
  <?php 
  if($event_stat_enable){
  ?>
  <div class="form-group">
	<table class="table table-striped table-hover table-condensed table-bordered">

		<tr>
			<th><?echo __('SER_NUM');?></th>
			<th><?echo __('NAME');?></th>
			<th><?echo __('refusing_ok');?></th>
			<th><?echo __('refusing_err');?></th>
			<th><?echo __('comment');?></th>
			
		</tr>
		<? 
		$count_event=0;
		$count_delete=0;
		$count_card=1;
		$total_items_count=0;
		
		$total_refusing_ok=0;//общий счетчик правильных отказов в проходе
		$total_refusing_err=0;//общий счетчик ошибочных отказов в проходе
		//echo Debug::vars('26', $event_stat);
		foreach ($event_stat as $key => $value)
		{
			//echo Debug::vars('26', $value);
			echo '<tr class="'.Arr::get($value, 'TR_COLOR', 'active').'">';
				echo '<td>'.$count_card.'</td>';
				echo '<td>'.HTML::anchor('event/device65/'.Arr::get($value, 'ID_DEV'), Arr::get($value, 'DOOR_NAME')).'(ID='.Arr::get($value, 'ID_DEV').')</td>';
				$refusing_ok=0;//счетчик правильных отказов в проходе
				$refusing_err=0;//счетчик ошибочных отказов в проходе
						foreach($value['USER'] as $aa=>$bb)
						{
							if($bb['IS_RIGHT'] == 1) 
							{
								$refusing_err++;
								$total_refusing_err++;
							} else {
								$refusing_ok++;
								$total_refusing_ok++;
							}
						};
						
				echo '<td>'. HTML::anchor('event/invalid/65', $refusing_ok). '</td>';
				echo '<td>';
					if($refusing_err>0) echo HTML::anchor('event/invalid/65', '<span class="label label-danger">'.__($refusing_err)).'</td>';
				echo '<td>';
					if(Arr::get($value, 'MODE_TEST')) echo '<span class="label label-danger">'.__('test_mode_is_on').'</span>';
					if(!Arr::get($value, 'DEV_ACTIVE')) echo '<span class="label label-warning">'.__('DEV_NOT_ACTIVE').'</span>';
					
				 echo '</td>';
				
				$count_event=$count_event+Arr::get($value, 'COUNT_EVENT', 0);
				
			echo '</tr>';
			$count_card++;
		}
		?>

		<tr>
			<td></td>
			<td><?echo __('total');?></td>
			<td><?echo $total_refusing_ok;?></td>
			<td><?echo $total_refusing_err;?></td>
		</tr>
	
	</table>
  </div>	
	<?php 
  } else {
  	echo __('windows_disable');
  }
	?>
  
	</div>
</div>