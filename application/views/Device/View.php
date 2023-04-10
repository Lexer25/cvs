<?php
	//echo Debug::vars('2', $device_data, $devtypeList);
?>
<script type="text/javascript">
$(function() {		
  		$("#table1").tablesorter({sortList:[[0,0]], headers: { }});
  	});
	
</script> 	
<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title"><?php echo __('device_data_desc');?></h3>
  </div>
 
  <div class="panel-body">
  

  <div class="form-group">
  	<?php
		
	
	echo Form::open('device/edit_devtype');
	?>
  
	<table id="table1" class=" table table-striped table-hover table-condensed tablesorter">
	<thead>
		<tr>
			<th><?echo __('SER_NUM');?></th>
			<th><?echo __('device_data_desc');?></th>
			<th><?echo __('VALUE');?></th>
			
						
		</tr>
	</thead>
	<tbody>

		<? 
		$nnum=1;
		echo '<tr>
				<td>'.$nnum++.'</td>'.
				'<td>'.__('id_dev').'</td>'.
				'<td>'.Arr::get($device_data, 'device_id').'</td>'.
				
			'</tr>';
			
		echo '<tr>
				<td>'.$nnum++.'</td>'.
				'<td>'.__('DEVICE_NAME').'</td>'.
				'<td>'.Arr::get($device_data, 'device_name').'</td>'.
				
			'</tr>';
			
		echo '<tr>
				<td>'.$nnum++.'</td>'.
				'<td>'.__('id_devtype').'</td>'.
				
				'<td>'.Form::hidden('id_dev', Arr::get($device_data, 'device_id')).
						Form::hidden('old_id_devtype', Arr::get($device_data, 'id_devtype')).
						Form::select('new_id_devtype', $devtypeList, Arr::get($device_data, 'id_devtype')).
						'</td>'.
			'</tr>';
			
		

		?>

	</tbody>
	</table>
	<?php 
	echo Form::button('todo', __('devtype_edit2'), array('value'=>'devtype_edit','class'=>'btn btn-success', 'type' => 'submit'));	
			
	
	echo Form::close(); ?>				

</div>	
	
  
</div>
</div>