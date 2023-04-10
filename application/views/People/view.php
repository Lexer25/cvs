<?php
//echo Debug::vars('2', $contact);
?>
<div class="panel panel-primary"> 

  <div class="panel-heading">
    <h3 class="panel-title"><?echo __('people_panel_title')?></h3>
  </div>
  <div class="panel-body">
	
<?// таблица общих данных о жильце?>
	<table class="table table-striped table-hover table-condensed table-bordered">
		<tr>
			<td>
			<?if (Arr::get($contact, 'PHOTO') != null) { ?>
				<img src="data:image/jpeg;base64,<?php echo base64_encode($contact['PHOTO']); ?>" height="200" alt="photo" />
				<?php } else { 
					echo HTML::image("images/nophoto.png", array('height' => 200, 'alt' => 'photo'));
			}
			
			?>
			</td>
			<td>
				<? 
				echo Arr::get($contact,'SURNAME').' '.Arr::get($contact, 'NAME').' '.Arr::get($contact,'PATRONYMIC').'<br>';
				echo __('tabmum'). ' '.Arr::get($contact, 'TABNUM', __('No_card')).'<br>';
				echo __('id_pep'). ' '.Arr::get($contact, 'ID_PEP', __('No_card')).'<br>';
				echo __('card'). ' '.Arr::get($contact, 'ID_CARD', __('No_card'));
				if (Arr::get($contact, 'ID_CARDTYPE') == 1) echo  '('. Model::factory('Stat')->reviewKeyCode(Arr::get($contact, 'ID_CARD', __('No_card'))).')';
				
				
				//вывод активности идентификатора
				if(Arr::get($contact, 'CARD_IS_ACTIVE', 0) == 1)
				{

					echo ' <span class="label label-success">'.__('card_status_is_active').'</span><br>';
				} else {

					echo ' <span class="label label-danger">'.__('card_status_status_is_not_active').'</span><br>';
				}
				echo __('card_type'). ' '.Arr::get($contact, 'CARDTYPE', __('CARDTYPE')).'<br>';
				
				if(Arr::get($contact, 'TIMESTART') != NULL)
				{
					echo __('card_timestart'). ' '. date("d.m.Y H:i", strtotime(Arr::get($contact, 'TIMESTART', __('No_card'))));
				} else {
					echo __('card_timestart'). ' n/a';
				}
				
				echo '<br>';
						
				if(Arr::get($contact, 'TIMEEND') != NULL)
				{
					echo __('card_timeend'). ' '. date("d.m.Y H:i", strtotime(Arr::get($contact, 'TIMEEND', __('No_card'))));
				} else {
					echo __('card_timeend'). ' n/a';
				}
				echo '<br>';
				if(Arr::get($contact, 'tree') != null)
				{
					echo __('org_tree'). ' '. Arr::get($contact, 'tree');
				} else {
					echo __('no_org_tree');
				}
				?>
				
			</td>	
			<td>
				
				<? // информация о типе авторизации
				
				echo __('about_pep_authmode'). '<br><br>';
				
				echo Model::factory('stat')->Authmode(Arr::get($contact, 'AUTHMODE', 0));
				
				echo Form::open('people/setAuthmetod');
				//echo Debug::vars('77', Model::Factory('stat')->authmodeList()); 
					echo Form::select('Authmode', Model::Factory('stat')->authmodeList(), Arr::get($contact, 'AUTHMODE', 0)).'<br>';
					echo Form::hidden('id_pep', Arr::get($contact, 'ID_PEP'));
					echo Form::hidden('id_card', Arr::get($contact, 'ID_CARD'));
					echo Form::submit(NULL, 'Authmode');
				echo Form::close();
				
				
				?>
				
			</td>	
			
		</tr>
	</table>
<?// таблица последний событий жильца?>
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title"><?echo __('people_event_title', array(':dateFrom'=>Arr::get($_SESSION, 'peopleEventsTimeFrom', date("d.m.Y H:m:s",strtotime("-1 days"))), ':dateTo'=>Arr::get($_SESSION, 'peopleEventsTimeTo', date("d.m.Y H:m:s"))))?></h3>
		</div>	
	<? //echo Debug::vars('88', $events);?>
	<table class="table table-striped table-hover table-condensed table-bordered">
					<tr>
						<th><?echo __('timestamp');?></th>
						<th><?echo __('door');?></th>
						<th><?echo __('card');?></th>
						<th><?echo __('note');?></th>
						<th><?echo __('event_name');?></th>
						<th><?echo __('event_analit');?></th>
					</tr>
						
				<?foreach ($events as $key=>$value)
				{
					$tr_color='warning';
					if(Arr::get($value, 'EVENT_ANALIT') == 0) $tr_color='success';
					echo '<tr class="'.$tr_color.'">';;
						echo '<td>'.date("d.m.Y H:i:s", strtotime(Arr::get($value, 'DATETIME'))).'</td>';
						echo '<td>'.Arr::get($value, 'DOOR_NAME').'(ID_DEV='.Arr::get($value, 'ID_DEV').')</td>';
						echo '<td>'.Arr::get($value, 'ID_CARD').'</td>';
						echo '<td>'.Arr::get($value, 'NOTE').'</td>';
						echo '<td>'.Arr::get($value, 'EVENT_NAME').'</td>';
						echo '<td>';
							echo(Arr::get($value, 'EVENT_ANALIT') == 1)? 'Да':'Нет';
							echo ' ('.Arr::get($value, 'ANALIT_CODE').' ';
							echo __(Arr::get($value, 'ANALIT_CODE').'a').')<br>';
							if(Arr::get($value, 'ANALIT_CODE') == 657) {
								$resultLoad=Arr::get($doors, Arr::get($value, 'ID_DEV'));
								echo '<small>';
								//echo Debug::vars('116', $resultLoad);
								echo __('load_result').': ';
								if(is_null(Arr::get($resultLoad, 'LOAD_TIME'))) {
									echo __('no_result_load_card_in_device', 
										array('LOAD_RESULT'=>Arr::get($resultLoad, 'LOAD_RESULT'), 
											'CONTROLLER_NAME'=> Arr::get($resultLoad, 'CONTROLLER_NAME'), 
											'DEVIDX'=> Arr::get($resultLoad, 'DEVIDX'),
											'ID_READER'=> Arr::get($resultLoad, 'ID_READER'), 
											'SERVER_NAME'=> Arr::get($resultLoad, 'SERVER_NAME'),
											'ID_DEV'=> Arr::get($resultLoad, 'ID_DEV')));
										echo __('no_date_for_load_card_in_device');
								} else { 
									echo __('result_load_card_in_device', 
										array('LOAD_RESULT'=>Arr::get($resultLoad, 'LOAD_RESULT'), 
										'CONTROLLER_NAME'=> Arr::get($resultLoad, 'CONTROLLER_NAME'), 
										'DEVIDX'=> Arr::get($resultLoad, 'DEVIDX'),
										'ID_READER'=> Arr::get($resultLoad, 'ID_READER'), 
										'SERVER_NAME'=> Arr::get($resultLoad, 'SERVER_NAME'),
										'ID_DEV'=> Arr::get($resultLoad, 'ID_DEV')));
									echo date("d.m.Y H:i:s", strtotime(Arr::get($resultLoad, 'LOAD_TIME')));
								}
									echo '</small>';
							}		//echo Debug::vars('114', $resultLoad);
																		
							echo '</td>';
					echo '</tr>';
					
				}
							
				?>
	</table>
	</div>
	
	<?// таблица загрузки карты жильца в контроллеры?>
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title"><?echo __('people_load_card')?></h3>
		</div>	
				<table class="table table-striped table-hover table-condensed table-bordered">
					<tr>
						<th><?echo __('SER_NUM');?></th>
						<th><?echo __('door');?></th>
						<th><?echo __('load_result');?></th>
						<th><?echo __('load_time');?></th>
						<th><?echo __('load_del');?></th>
						<th><?echo __('load_insert');?></th>
				<?
				$row_count=1;
				//echo Debug::vars('139', $doors);
				foreach ($doors as $key=>$value)
				{
					echo '<tr>';
						echo '<td>'.$row_count++.'</td>';
						echo '<td>'.Arr::get($value, 'NAME').'('.Arr::get($value, 'ID_DEV').')'.' '.Arr::get($value, 'STANDALONE').' </td>';
						
						if(Arr::get($value, 'STANDALONE') == 0){
							echo '<td>'.__('standalone').'</td>';
							echo '<td>--</td>';
						} else {
							if(is_null(Arr::get($value, 'LOAD_TIME'))) {
								echo '<td>'.__('no_result_load_card_in_device', array('LOAD_RESULT'=>Arr::get($value, 'LOAD_RESULT'), 'CONTROLLER_NAME'=> Arr::get($value, 'CONTROLLER_NAME'), 'DEVIDX'=> Arr::get($value, 'DEVIDX'),'ID_READER'=> Arr::get($value, 'ID_READER'), 'SERVER_NAME'=> Arr::get($value, 'SERVER_NAME'))).'</td>';
								echo '<td>'.__('no_date_for_load_card_in_device').'</td>';
							} else { 
								echo '<td>'.__('result_load_card_in_device', array('LOAD_RESULT'=>Arr::get($value, 'LOAD_RESULT'), 'CONTROLLER_NAME'=> Arr::get($value, 'CONTROLLER_NAME'), 'DEVIDX'=> Arr::get($value, 'DEVIDX'),'ID_READER'=> Arr::get($value, 'ID_READER'), 'SERVER_NAME'=> Arr::get($value, 'SERVER_NAME'))).'</td>';
								echo '<td>'.date("d.m.Y H:i:s", strtotime(Arr::get($value, 'LOAD_TIME'))).'</td>';
							}
						}	
						
						echo '<td>';
							if(isset($value['LOAD_DEL'])) echo date("d.m.Y H:i:s", strtotime(Arr::get($value, 'LOAD_DEL')));
						echo '</td>';
						echo '<td>';
							if(Arr::get($value, 'LOAD_INSERT')==1) {
							echo HTML::image('static\images\green-check.png', array('alt' => 'Карта стоит в очереди на загрузку', 'title'=>Arr::get($value, 'TIME_INSERT')));
							} else {
								echo __('no');
							}
							
						echo '</td>';
						
					echo '</tr>';
					
				}
							
				?>
				</table>
	</div>

</div>	
</div>
	
  

