<script type="text/javascript">
$(function() {		
  		$("#table1").tablesorter({sortList:[[0,0]], headers: { 0:{sorter: false}}});
  	});
	
</script> 	
<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title"><?echo __('event_panel_title', array('count_event'=> count($list)))?></h3>
  </div>
  <div class="panel-body">
  
  
  <?
  
  echo __('event_panel_desc');?>
  <div class="form-group">
	<table id="table1" class=" table table-striped table-hover table-condensed tablesorter">
	<thead>
		<tr>
			<th><?echo __('SER_NUM');?></th>
			<th><?echo __('DATE_EVENT');?></th>
			<th><?echo __('NAME');?></th>
			<th><?echo __('NAME_EVENT');?></th>
			<th><?echo __('PEOPLE');?></th>
			<th><?echo __('ANALIS');?></th>
			<th><?echo __('LOAD_RESULT');?></th>
			<th><?echo __('time_compare');?></th>
		</tr>
	</thead>
	<tbody>
		<? 
		$count_event=0;
		$count_delete=0;
		$count_card=1;
		$total_items_count=0;
		//echo Debug::vars('27', $list);
		foreach ($list as $key => $value)
		{
			$tr_color=Arr::get($value, 'TR_COLOR', 'active');
			if(strtotime(Arr::get($value, 'LOAD_TIME')> strtotime(Arr::get($value, 'DATETIME')))) $tr_color='Info';
			if(strtotime(Arr::get($value, 'LOAD_TIME')) == NULL ) $tr_color='Danger';
			
			echo '<tr class="'.Arr::get(Arr::get($value, 'HINT'), 'tr_color').'">';
			
				echo '<td>'.$count_card.'</td>';
				echo '<td>'.date("d.m.Y H:i", strtotime(Arr::get($value, 'DATETIME'))).'</td>';
				echo '<td>'.HTML::anchor('door/doorInfo/'.Arr::get($value, 'ID_DEV'), Arr::get($value, 'DOOR_NAME').'(ID='.Arr::get($value, 'ID_DEV')).')</td>';
				echo '<td>'.Arr::get($value, 'NAME_EVENT', 'No data').'</td>';
				echo '<td>'.HTML::anchor('people/peopleInfo/'.Arr::get($value, 'ESS1'), Arr::get($value, 'PEP_NAME')).'<br>(ID_PEP='.Arr::get($value, 'ESS1').', '.Arr::get($value, 'ID_CARD').')</td>';
				echo '<td>'.__('result_analis_'.Arr::get($value, 'IS_RIGHT')).'</td>';
				if(Arr::get($value, 'LOAD_TIME') == NULL) {
					echo '<td>'.__('no_load_time').'</td>';
					} else {
				echo '<td>'.__('load_result_mess', array(
					':LOAD_TIME' => date("d.m.Y H:i", strtotime(Arr::get($value, 'LOAD_TIME', 'no_data'))),
					':LOAD_RESULT' => Arr::get($value, 'LOAD_RESULT', 'no_data'),
					':DEVIDX' => Arr::get($value, 'DEVIDX', 'no_data'),
					))
					.'</td>';}
				//echo $message;
				echo '<td>'.Arr::get(Arr::get($value, 'HINT'), 'hint').'<br><p class="text-muted">(Reason='.Arr::get(Arr::get($value, 'HINT'), 'id').')</p></td>';
				
			echo '</tr>';
			$count_card++;
		}
		?>

	</tbody>
	</table>
				

</div>	
	
  
</div>
</div>