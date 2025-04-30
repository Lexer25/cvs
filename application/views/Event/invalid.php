<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title"><?echo __('event_panel_title')?></h3>
  </div>
  <div class="panel-body">
  
  
  <?
  //echo Debug::vars('9', $list);
  echo __('event_panel_desc');?>
  <div class="form-group">
	<table class="table table-striped table-hover table-condensed table-bordered">

		<tr>
			<th><?echo __('SER_NUM');?></th>
			<th><?echo __('NAME');?></th>
			<th><?echo __('NAME_EVENT');?></th>
			<th><?echo __('refusing_ok');?></th>
			<th><?echo __('refusing_err');?></th>
			
		</tr>
		<? 
		$count_event=0;
		$count_delete=0;
		$count_card=1;
		$total_items_count=0;
		
		
		foreach ($list as $key => $value)
		{
			echo '<tr class="'.Arr::get($value, 'TR_COLOR', 'active').'">';
				echo '<td>'.$count_card.'</td>';
				echo '<td>'.Arr::get($value, 'DOOR_NAME').'(ID='.Arr::get($value, 'ID_DEV').')</td>';
				echo '<td>'.Arr::get($value, 'NAME_EVENT', 'No data').'</td>';
				//echo '<td>'.Arr::get($value, 'P_SURNAME').' '.Arr::get($value, 'P_NAME').' '.Arr::get($value, 'P_PATRONYMIC').'</td>';
				$refusing_ok=0;//счетчик правильных отказов в проходе
				$refusing_err=0;//счетчик ошибочных отказов в проходе
						foreach($value['USER'] as $key=>$value)
						{
							if($value['IS_RIGHT']) 
							{
								$refusing_err++;
							} else {
								$refusing_ok++;
							}
								
							
						};
			echo '<td>'. $refusing_ok . '</td>';
			echo '<td>'. $refusing_err.'</td>';
				$count_event=$count_event+Arr::get($value, 'COUNT_EVENT', 0);
				
			echo '</tr>';
			$count_card++;
		}
		?>

		<tr>
			<td></td>
			<td><?echo __('total')?></td>
			<td><?echo count($list);?></td>
			<td><?echo $total_items_count;?></td>
			<td></td>
			
			
		</tr>
	
	</table>
  
	<table class="table table-striped table-hover table-condensed table-bordered">

		<tr>
			<th><?echo __('SER_NUM');?></th>
			<th><?echo __('NAME');?></th>
			<th><?echo __('NAME_EVENT');?></th>
			<th><?echo __('DOOR');?></th>
		</tr>
		<? 
		$count_event=0;
		$count_delete=0;
		$count_card=1;
		$total_items_count=0;
		//echo Debug::vars('27', $list);
		foreach ($list as $key => $value)
		{
			echo '<tr class="'.Arr::get($value, 'TR_COLOR', 'active').'">';
				echo '<td>'.$count_card.'</td>';
				echo '<td>'.Arr::get($value, 'DOOR_NAME').'(ID='.Arr::get($value, 'ID_DEV').')</td>';
				echo '<td>'.Arr::get($value, 'NAME_EVENT', 'No data').'</td>';
				//echo '<td>'.Arr::get($value, 'P_SURNAME').' '.Arr::get($value, 'P_NAME').' '.Arr::get($value, 'P_PATRONYMIC').'</td>';
				echo '<td>';?>
					 <table class="table table-striped table-hover table-condensed table-bordered">
					 <tr>
						<th><?echo __('UNLEGAL');?></th>
						<th><?echo __('LEGAL');?></th>
					</tr>
					<?
					$lc=0;
						foreach($value['USER'] as $key=>$value)
						{
							$color='<p class="text-success">';
							$mess= $value['P_SURNAME'].' '.$value['P_NAME'].' '.$value['P_PATRONYMIC'].' (ID_PEP='.$value['ESS1'].','.$value['ID_CARD'].')';
							if($value['IS_RIGHT'])
							{
								echo  '<tr>';
									echo '<td></td>';
									echo '<td>';
									if ($value['PHOTO'] != null) { ?>
										<img src="data:image/jpeg;base64,<?php echo base64_encode($value['PHOTO']); ?>" height="100" alt="photo" />
										<?php } else { 
										echo HTML::image("images/nophoto.png", array('height' => 100, 'alt' => 'photo'));
									};
									echo HTML::anchor('people/peopleInfo/'.$value['ESS1'],$mess, array('class'=>'text-danger bold') ).'</p><br></td>';
									
								echo '</tr>';
							} else {
								echo  '<tr>';
									
									echo '<td>';
									if ($value['PHOTO'] != null) { ?>
										<img src="data:image/jpeg;base64,<?php echo base64_encode($value['PHOTO']); ?>" height="100" alt="photo" />
										<?php } else { 
										echo HTML::image("images/nophoto.png", array('height' => 100, 'alt' => 'photo'));
									};
									echo HTML::anchor('people/peopleInfo/'.$value['ESS1'], $mess, array('class'=>'text-success')).'</p><br></td>';
									echo '<td></td>';
								echo '</tr>';
							}
								
							$lc++;
						};
					echo '</table>';
			echo '</td>';
				$count_event=$count_event+Arr::get($value, 'COUNT_EVENT', 0);
				
			echo '</tr>';
			$count_card++;
		}
		?>

		<tr>
			<td></td>
			<td><?echo __('total')?></td>
			<td><?echo count($list);?></td>
			<td><?echo $total_items_count;?></td>
			<td></td>
			
			
		</tr>
	
	</table>
				

</div>	
	
  
</div>
</div>