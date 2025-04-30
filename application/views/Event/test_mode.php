<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title"><?echo __('test_mode_panel_title')?></h3>
  </div>
  <div class="panel-body">
  

  <div class="form-group">
	<table class="table table-striped table-hover table-condensed table-bordered">

		<tr>
			<th><?echo __('SER_NUM');?></th>
			<th><?echo __('DEVICE');?></th>
			<th><?echo __('count_event');?></th>
			<th><?echo __('door');?></th>
			<th><?echo __('NAME_EVENT');?></th>
			
		</tr>
		<? 
		$count_event=0;
		$count_delete=0;
		$count_card=1;
		$total_items_count=0;
		
		
		foreach ($list as $key => $value)
		{
			echo '<tr>';
				echo '<td>'.$count_card.'</td>';
				echo '<td>'.iconv('windows-1251','UTF-8', (Arr::get($value, 'DEVICE_NAME'))).'</td>';
				echo '<td>'.Arr::get($value, 'COUNT', 'No data').'</td>';
				echo '<td>'.iconv('windows-1251','UTF-8',Arr::get($value, 'NAME', 'No data')).'</td>';
				echo '<td>'.iconv('windows-1251','UTF-8',Arr::get($value, 'NAME_EVENT', 'No data')).'</td>';
				
			echo '</tr>';
			$count_card++;
		}
		?>

		
	
	</table>
  
				

</div>	
	
  
</div>
</div>