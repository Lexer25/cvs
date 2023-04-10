<div class="panel panel-primary">
	  <div class="panel-heading">
		<h3 class="panel-title"><?echo __('check_panel_title')?></h3>
	  </div>
	  <div class="panel-body">
		 
		<?echo Form::open('check/selector');?>
		  <br />
		<div class="panel panel-primary">
			<div class="panel-heading"><?echo __('select_device');?></div>
			<div class="panel-body">
				<div class="panel-body">
				
					<div class="input-group col-xs-4">
					<span class="input-group-addon">Server</span>
					  <select class="form-control" name="id_server">
					  <? if (isset($server_list))
					  {
						foreach ($server_list as $key=>$value)
							{
								$select='';
								if(isset($server_select) and (Arr::get($value, 'ID_SERVER') == $server_select)) $select= 'selected="selected"';
								echo '<option value="'.Arr::get($value, 'ID_SERVER').'" '.$select.' >'.iconv('windows-1251','UTF-8',Arr::get($value, 'NAME')).'</option>';
							}
					  } else {
						  
						  echo '<option value=0>no</option>';
						 }
						   ?>
						  </select>
					</div>
					
					<div class="input-group col-xs-4">
					<span class="input-group-addon">Device</span>
					  <select class="form-control" name="device_name">
						<? if (isset($device_list))
					  {
						$i=0;
						//Вывод списка устройств
						foreach ($device_list as $key=>$value)
							{
								$select='';
								$codi=mb_detect_encoding($value);
								
								echo '<option value='.$i.' >'.iconv('windows-1251','UTF-8',$value).'</option>';
								$i++;
							}
					  } else {
						  
						  echo '<option value=0>no</option>';
						 }
						   ?>
					  </select>
					</div>
				</div>
				<div class="btn-group">
					<? 
					$dis='';
					if(empty($server_list)) $dis='disabled="disabled"';
					echo ' <button type="submit" class="btn btn-primary" name="getDeviceList" value="1" title = "Получить список контроллеров" '.$dis.'>'.__('getDeviceList').'</button>' ;?>
				</div>
				
			</div>
		</div>
					<br />
					<br />
			 
			
			<div class="panel panel-primary">
			  <div class="panel-heading"><?echo __('parameter_of_test_device');?></div>
				<div class="panel-body">
					<?echo __('mess_about_test_device', array(
						':device_name' => Kohana::$config->load('artonitcity_config')->name_device_fro_test,
						':title_dev' => __('select_device'),
						));?>
					<div class="panel panel-success">
					  <div class="panel-heading"><?echo __('parameter_of_read_cell');?></div>
					  <div class="panel-body">
						<div class="panel-body">
							<div class="input-group col-xs-2">
								<span class="input-group-addon">Door</span>
								<select class="form-control" name="door">
								  <option value="0">0</option>';
								  <option value="1">1</option>';
								</select>
							</div>
					 
							 <div class="input-group col-xs-2">
							  <span class="input-group-addon">Cell from</span>
							  <input type="text" class="form-control" placeholder="Cell from" name="cellfrom">
							</div>

							 <div class="input-group col-xs-2">
							  <span class="input-group-addon">Cell to</span>
							  <input type="text" class="form-control" placeholder="Cell to" name="cellto">
							</div>
						</div>
						<div class="btn-group">
							<? $dis='';
							if(empty($device_list)) $dis='disabled="disabled"';
							echo ' <button type="submit" class="btn btn-primary" name="read_data_from_device"  value="1" title = "Прочитать даные из устройства" '.$dis.'>'. __('read_data_from_device').'</button>';?>
						</div>
					</div>
					</div>
					
					<div class="panel panel-danger">
						<div class="panel-heading"><?echo __('parameter_of_write_cell');?></div>
						<div class="panel-body">
						<?echo __('note_fro_write_cell');?>
						<div class="panel-body">
							<div class="input-group col-xs-2">
								<span class="input-group-addon">Door</span>
								<select class="form-control" name="door">
								  <option value="0">0</option>';
								  <option value="1">1</option>';
								</select>
							</div>
					 
							 <div class="input-group col-xs-2">
							  <span class="input-group-addon">Cell from</span>
							  <input type="text" class="form-control" placeholder="Cell from" name="cellfrom_write">
							</div>

							 <div class="input-group col-xs-2">
							  <span class="input-group-addon">Cell to</span>
							  <input type="text" class="form-control" placeholder="Cell to" name="cellto_write">
							</div>
						</div>
						<div class="btn-group">
							<? $dis='';
							if(empty($device_list)) $dis='disabled="disabled"';
							echo '  <button type="submit" class="btn btn-primary" name="write_data_to_device"  value="1" title = "Прочитать даные из устройства" '.$dis.'>'. __('write_data_to_device').'</button>';?>
						</div>
						</div>
					</div>
				</div>
			</div>
			
		<label class="btn btn-primary"  for="modalm-1">Modal windows</label>
		
		<?echo Form::close();?>	
		
	</div>
</div>

<div class="modalm">
	<input class="modalm-open" id="modalm-1" type="checkbox" hidden>
	<div class="modalm-wrap" aria-hidden="true" role="dialog">
		<label class="modalm-overlay" for="modalm-addNote"></label>
		<div class="modalm-dialog">
			<div class="modalm-header">
				<h2>Добавление записи</h2>
				<label class="btnm-close" for="modalm-1" aria-hidden="true">x</label>
			</div>
			<div class="modalm-body">
				<?
				echo Form::open('check/selector');
					echo Debug::vars('150', $_POST);
					echo __('Time_for_load_is').(Arr::get($_POST, 'cellto').' '.Arr::get($_POST, 'cellfrom'));
					echo __('Time_for_load_is'). Arr::get($_POST, 'cellto') - Arr::get($_POST, 'cellfrom').'<br />';
					echo ' <button type="submit" class="btn btn-primary" name="getDeviceList" value="1" title = "Получить список контроллеров">'.__('getDeviceList').'</button>' ;
				echo Form::close();
					?>

			</div>
			<div class="modalm-footer">
				<h2>footer</h2>
				
			</div>
		</div>
	</div>
</div>
				
