<script type="text/javascript">
$(function() {		
  		$("#table1").tablesorter({sortList:[[0,0]], headers: { }});
  	});
	
</script> 	
<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title"><?echo __('not_active_device_panel_desc');?></h3>
  </div>
 
  <div class="panel-body">
  
     <div class="panel panel-danger">
  <div class="panel-heading">
    <h3 class="panel-title"><?php echo __('not_active_device_panel_desc');?></h3>
  </div>
  <div class="panel-body">
    <?php echo __('guid_not_active_device_panel_desc');?>
  </div>
  </div>

  <div class="form-group">
	<table id="table1" class=" table table-striped table-hover table-condensed tablesorter">
	<thead>
		<tr>
			<th><?echo __('SER_NUM');?></th>
			<th><?echo __('NAME');?></th>
			<th><?echo __('CONTROLLER_NAME');?></th>
			<th><?echo __('SERVER_NAME');?></th>
			<th><?echo __('COUNT');?></th>
						
		</tr>
	</thead>
	<tbody>
		<? 
		
		$count_card=1;
		
		
		//echo Debug::vars('37', $ListNotActiveDevices);
		foreach ($ListNotActiveDevices as $key => $value)
		{
			$tr_color=Arr::get($value, 'TR_COLOR', 'active');
					
			echo '<tr class="'.Arr::get(Arr::get($value, 'HINT'), 'tr_color').'">';
			
				echo '<td>'.$count_card.'</td>';
				echo '<td>'.HTML::anchor('door/doorInfo/'.Arr::get($value, 'ID_DEV'), Arr::get($value, 'NAME', 'no_data')).'</td>';
				echo '<td>'.Arr::get($value, 'CONTROLLER_NAME').')</td>';
				echo '<td>'.Arr::get($value, 'SERVER_NAME', 'No data').'</td>';
				echo '<td>'.Arr::get($value, 'COUNT').'</td>';
				
			$count_card++;
		}
		?>

	</tbody>
	</table>
				

</div>	
	
  
</div>
</div>