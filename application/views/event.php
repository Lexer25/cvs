<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title"><?
    $count_event= isset($list)? count($list) : '0'; 
    echo __('event_panel_title', array('count_event'=>$count_event))?></h3>
  </div>
  <div class="panel-body">
  
  
  <?
  

  echo HTML::anchor('event/analit_list', __('analit_list')).' '.$analit_count;
  //echo Debug::vars('9', $list);
  echo '<br>';
  echo __('event_panel_desc');?>
  <div class="form-group">
	<table class="table table-striped table-hover table-condensed">

		<tr>
			
			<th><?echo __('ID_EVENTTYPE');?></th>
			<th><?echo __('NAME_EVENT');?></th>
			<th><?echo __('COUNT_EVENT');?></th>
			
		</tr>
		<? 
		$count_event=0;
		$count_delete=0;
		foreach ($list as $key => $value)
		{
			echo '<tr class="'.Arr::get($value, 'TR_COLOR', 'active').'">';
				echo '<td>'.Arr::get($value, 'ID_EVENTTYPE', 'No data').'</td>';
				echo '<td>';
				switch(Arr::get($value, 'ID_EVENTTYPE')){
					case	46 : echo HTML::anchor('event/unknowcard/'.Arr::get($value, 'ID_EVENTTYPE'), Arr::get($value, 'NAME_EVENT', 'No data'));
						break;
					
					case	65 : echo HTML::anchor('event/invalid/'.Arr::get($value, 'ID_EVENTTYPE'), Arr::get($value, 'NAME_EVENT', 'No data'));
						break;
						
					case	68 : echo HTML::anchor('event/68/'.Arr::get($value, 'ID_EVENTTYPE'), Arr::get($value, 'NAME_EVENT', 'No data'));
					
					//не пропущен по времени errtz
					case	47 : echo HTML::anchor('event/errtz/'.Arr::get($value, 'ID_EVENTTYPE'), Arr::get($value, 'NAME_EVENT', 'No data'));
						break;
					
					//проход в режиме Тест
					case	145 : echo HTML::anchor('event/test_mode/'.Arr::get($value, 'ID_EVENTTYPE'), Arr::get($value, 'NAME_EVENT', 'No data'));
						break;
						
					//Проход неизвестного идентификатора
					case	80 : echo HTML::anchor('event/unknowcard/'.Arr::get($value, 'ID_EVENTTYPE'), Arr::get($value, 'NAME_EVENT', 'No data'));
					break;
					
					default:
						echo Arr::get($value, 'NAME_EVENT', 'No data');
				}
				echo	'</td>';
				echo '<td>'.Arr::get($value, 'COUNT_EVENT', '-').'</td>';
				$count_event=$count_event+Arr::get($value, 'COUNT_EVENT', 0);
				
			echo '</tr>';
			
		}
		?>
		<tr>
			<td></td>
			<td></td>
			
			<td><?echo $count_event;?></td>
			
		</tr>
		<tr>
			<td></td>
			<td><?echo __('total')?></td>
			<td><?echo $count_event;?></td>
			<td></td>
			
		</tr>
	
	</table>
				

</div>	
	
  
</div>
</div>