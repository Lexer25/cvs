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
			<th><?echo __('ID_EVENTTYPE');?></th>
			<th><?echo __('NAME_EVENT');?></th>
			<th><?echo __('DOOR');?></th>
			
		</tr>
		</thead>
		<tbody>
		<? 
		$count_event=0;
		$count_delete=0;
		$count_card=1;
		$total_items_count=0;
		
		foreach ($list as $key => $value)
		{
			echo '<tr class="'.Arr::get($value, 'TR_COLOR', 'active').'">';
				echo '<td>'.$count_card.'</td>';
				echo '<td>'.Arr::get($value, 'ID_CARD', 'No data').'</td>';
				echo '<td>'.Arr::get($value, 'NAME_EVENT', 'No data').'</td>';
				echo '<td>';
				$lc=0;
					foreach($value['ID_DEV'] as $key=>$value)
					{
						echo $value['NAME'].' '.$value['COUNT'].__('times').'<br>';
						echo __('total').' '.$lc=$lc+$value['COUNT'].__('times');
						$total_items_count=$total_items_count+$value['COUNT'];
						echo '<br>';
					};
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
	<tbody>
	</table>
				<button type="submit" class="btn btn-primary" disabled="disabled">Загрузить</button>

</div>	
	
  
</div>
</div>