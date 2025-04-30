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
  //echo Debug::vars('9', $list);
  echo __('event_panel_desc');?>
  <div class="form-group">
	<table id="table1" class=" table table-striped table-hover table-condensed tablesorter">
		<thead>
		<tr>
			
			<th><?echo __('SER_NUM');?></th>
			<th><?echo __('DATETIME');?></th>
			<th><?echo __('DOOR_NAME');?></th>
			<th><?echo __('ID_CARD');?></th>
			<th><?echo __('ORG_NAME');?></th>
			<th><?echo __('PEOPLE_NAME');?></th>
			<th><?echo __('PEOPLE_NOTE');?></th>
			
		</tr>
		</thead>
		<tbody>
		<? 
		$count_card=1;
		
		
		foreach ($list as $key => $value)
		{
			echo '<tr class="'.Arr::get($value, 'TR_COLOR', 'active').'">';
				echo '<td>'.$count_card.'</td>';
				echo '<td>'.date("d.m.Y H:i:s", strtotime(Arr::get($value, 'DATETIME'))).'</td>';
				echo '<td>'.Arr::get($value, 'DOOR_NAME', 'No data').'</td>';
				echo '<td>'.Arr::get($value, 'ID_CARD', 'No data').'</td>';
				echo '<td>'.Arr::get($value, 'ORG_NAME', 'No data').'</td>';
				echo '<td>'.HTML::anchor('people/peopleInfo/'.Arr::get($value, 'ESS1'),
				Arr::get($value, 'NAME', 'No data').' '.Arr::get($value, 'SURNAME', 'No data').' '.Arr::get($value, 'PATRONYMIC', 'No data')
				).'</td>';
				echo '<td>'.Arr::get($value, 'PEOPLE_NOTE', 'No data').'</td>';
						
			echo '</tr>';
			$count_card++;
		}
		?>

	<tbody>
	</table>

</div>	
	
  
</div>
</div>