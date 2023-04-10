<? //http://itchief.ru/lessons/bootstrap-3/30-bootstrap-3-tables;?>
<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title"><?echo __('Load_panel_title')?></h3>
  </div>
  <div class="panel-body">
  
    <?echo Form::open('Dashboard/device_control');?>
	<table class="table table-striped table-hover table-condensed">


		<tr>
			<th><?echo Form::checkbox('all', 1, FALSE) . __('SERVER_NAME');?></th>
			<th><?echo __('SERVER_NAME');?></th>
			<th><?echo __('SERVER_IP');?></th>
			<th><?echo __('SERVER_PORT');?></th>
			<th><?echo __('DEVICE_NAME');?></th>
			<th><?echo __('DEVICE_VERSION');?></th>
			<th><?echo __('DOOR_NAME');?></th>
			<th><?echo __('BASE_COUNT');?></th>
			<th><?echo __('DEVICE_COUNT');?></th>
			
		</tr>
		<? foreach ($list as $key => $value)
		{
			echo '<tr class="'.Arr::get($value, 'TR_COLOR', 'active').'">';
				echo '<td>'.Form::checkbox('id_dev['.Arr::get($value, 'ID_DOOR').']', 1, FALSE).'</td>';
				echo '<td>'.Arr::get($value, 'ID_TS').', '.Arr::get($value, 'SERVER_NAME', 'No data').'</td>';
				echo '<td>'.Arr::get($value, 'SERVER_IP', 'No data').'</td>';
				echo '<td>'.Arr::get($value, 'SERVER_PORT', 'No data').'</td>';
				echo '<td>'.Arr::get($value, 'ID_DEV').', '.Arr::get($value, 'DEVICE_NAME', 'No data').'</td>';
				echo '<td>'.Arr::get($value, 'DEVICE_VERSION', 'No data').'</td>';
				echo '<td>'.Arr::get($value, 'ID_DOOR').', '.Arr::get($value, 'DOOR_NAME', 'No data').'</td>';
				echo '<td>'.Arr::get($value, 'BASE_COUNT', 'No data').'</td>';
				echo '<td>'.Arr::get($value, 'DEVICE_COUNT', 'No data').'</td>';
			echo '</tr>';
			
		}
		
		
		?>
	</table>
	<button type="submit" class="btn btn-primary col-md-offset-1" name="synctime" value="1"><?echo __('synctime')?></button>
	<button type="submit" class="btn btn-primary" name="load_card"  value="1"><?echo __('load_card')?></button>
	<button type="submit" class="btn btn-primary" name="clear_device"  value="1"><?echo __('clear_device')?></button>
	<button type="submit" class="btn btn-primary" name="synctime1"  value="1"><?echo __('synctime1')?></button>
	<button type="submit" class="btn btn-primary" name="checkStatus"  value="1"><?echo __('checkStatus')?></button>
<?echo Form::close();?>	
  </div>
</div>