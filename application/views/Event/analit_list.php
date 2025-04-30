<script type="text/javascript">
$(function() {		
  		$("#table1").tablesorter({sortList:[[0,0]], headers: { 0:{sorter: false}}});
  	});
	
</script> 	
<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title"><?echo __('analit_panel_title', array('count_event'=> count($list)))?></h3>
  </div>
	<div class="panel-body">
		<?
			echo __('event_panel_desc');?>
	</div>
  <div class="form-group">
	<div class="panel panel-primary" id="refresh">
		<div class="panel-heading"><?echo __('device_error_analyt');?></div>
			<div class="panel-body">
				<table id="table1" class=" table table-striped table-hover table-condensed tablesorter">
					<thead>
						<tr>
							<th><?echo __('SER_NUM');?></th>
							<th><?echo __('DATE_EVENT');?></th>
							<th><?echo __('NAME');?></th>
							<th><?echo __('NAME_EVENT');?></th>
							<th><?echo __('PEOPLE');?></th>
							<th><?echo __('ANALIT_CODE');?></th>
							<th><?echo __('ANALIS');?></th>
							<th><?echo __('LOAD_RESULT');?></th>
							
						</tr>
					</thead>
				<tbody>
				<? 
			
					$count_card=1;
					
					//echo Debug::vars('37', $list);
					foreach ($list as $key => $value)
					{
						$tr_color=Arr::get($value, 'TR_COLOR', 'active');
						if(strtotime(Arr::get($value, 'LOAD_TIME')> strtotime(Arr::get($value, 'DATETIME')))) $tr_color='Info';
						if(strtotime(Arr::get($value, 'LOAD_TIME')) == NULL ) $tr_color='Danger';
						
						echo '<tr class="'.Arr::get(Arr::get($value, 'HINT'), 'tr_color').'">';
						
							echo '<td>'.$count_card.'</td>';
							echo '<td>'.date("d.m.Y H:i", strtotime(Arr::get($value, 'DATETIME'))).'</td>';
							echo '<td>'.HTML::anchor('door/doorInfo/'.Arr::get($value, 'ID_DEV'), Arr::get($value, 'DOOR_NAME').'(ID='.Arr::get($value, 'ID_DEV')).')</td>';
							echo '<td>'.Arr::get($value, 'EVENT_NAME', 'No data').'</td>';
							echo '<td>'.HTML::anchor('people/peopleInfo/'.Arr::get($value, 'ESS1'), Arr::get($value, 'NOTE')).'<br>(ID_PEP='.Arr::get($value, 'ESS1').', '.Arr::get($value, 'ID_CARD').')</td>';
							echo '<td>'.Arr::get($value, 'ANALIT').'</td>';
							echo '<td>'.__(Arr::get($value, 'ANALIT').'a').'</td>';
							if(Arr::get($value, 'LOAD_TIME') == NULL) {
								echo '<td>'.__('no_load_time').'</td>';
								} else {
							echo '<td>'.__('load_result_mess', array(
								':LOAD_TIME' => date("d.m.Y H:i", strtotime(Arr::get($value, 'LOAD_TIME', 'no_data'))),
								':LOAD_RESULT' => Arr::get($value, 'LOAD_RESULT', 'no_data'),
								':DEVIDX' => Arr::get($value, 'DEVIDX', 'no_data'),
								))
								.'</td>';}
							
							
						echo '</tr>';
						$count_card++;
					}
				?>

				</tbody>
				</table>
			</div>
		</div>			

</div>	
	
  
</div>
</div>