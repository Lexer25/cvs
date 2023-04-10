<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title"><?echo __('Панель управления')?></h3>
  </div>
  <div class="panel-body">

<!--  Панель №1 с информацией по жильцами и картам. -->
		<div class="panel panel-info col-md-3">
			<div class="panel-heading row"><?echo __('data_people_and_card');?></div>
			<div class="panel-body">
			<?
			if(isset($list['card']) and count($list['card']))
			{
				echo Arr::get($list['card']['key_count'], 'name').' '. Arr::get($list['card']['key_count'], 'count').'<br>';
				echo Arr::get($list['card']['key_people'], 'name').' '. Arr::get($list['card']['key_people'], 'count').'<br>';
				echo Arr::get($list['card']['key_people_delete'], 'name').' '. Arr::get($list['card']['key_people_delete'], 'count').'<br>';
				echo Arr::get($list['card']['event_count_24'], 'name').' '. Arr::get($list['card']['event_count_24'], 'count').'<br>';
				echo Arr::get($list['card']['count_card_late_next_week'], 'name').' '. HTML::anchor('people/find_card_late_next_week',Arr::get($list['card']['count_card_late_next_week'], 'count')).'<br>';
				echo Arr::get($list['card']['count_card_late'], 'name').' '.HTML::anchor('people/find_card_late', Arr::get($list['card']['count_card_late'], 'count')).'<br>'; 
				echo Arr::get($list['card']['people_without_card'], 'name').' '.HTML::anchor('people/people_without_card', Arr::get($list['card']['people_without_card'], 'count')).'<br>';
				echo Arr::get($list['card']['count_unactive_card'], 'name').' '.HTML::anchor('people/find_unActiveCard', Arr::get($list['card']['count_unactive_card'], 'count'), array('class'=>'disabled')).'<br>';
					} else {
				echo __('windows_disable');
			}
			?>
			</div>
			
		</div>

<!--  Панель №2. Информация по оборудованию. -->

		<div class="panel panel-warning col-md-3">
		  <div class="panel-heading  row"><?echo __('data_device');?></div>
		  <div class="panel-body">
			<?
			if(isset($list['device']) and count($list['device']))
			{
				foreach (Arr::get($list, 'device') as $key=>$value)
				{
				echo Arr::get($value, 'name').' '.Arr::get($value, 'count').'<br>';	
				}
			} else {
				echo __('windows_disable');
			}
			
			?>
		  </div>
		</div>

<!--  Панель №3. Очередь загрузок. -->
		
		<div class="panel panel-success col-md-3">
		  <div class="panel-heading  row">
			<h3 class="panel-title"><?echo __('data_cardindev');?></h3>
		  </div>
		  <div class="panel-body">
			<?
			//echo debug::vars('58', $list['order'] );
			if(isset($list['order']) and count($list['order']))
			{ 
				foreach ($list['order'] as $key=>$value)
				{
					if($key == 'card_for_not_active') 
					{
						echo $value['name'].' '.HTML::anchor('device/'.$key,$value['count']).'<br>';	
					} else {
						echo $value['name'].' '.$value['count'].'<br>';
					}
				}
			} else {
				echo __('windows_disable');
			}
			?>
		  </div>
		</div>
		
<!--  Панель №4. События. -->
		
		<div class="panel panel-info col-md-3">
		  <div class="panel-heading  row">
			<h3 class="panel-title"><?echo __('event_stat');?></h3>
		  </div>
		  <div class="panel-body">
			<?
			if($event_stat_enable){
				if(isset($event_stat) and count($event_stat))
				{
					foreach ($event_stat as $key=>$value)
					{
						echo __('event_stat_for_dashboard', array(
							':name'	=> $value['NAME_EVENT'], 
							':count'	=> $value['COUNT_EVENT'], 
							)).'<br>';
					}
				}	 else {
					echo __('no_dashboard_events');	
				}
			} else {
				echo __('windows_disable');
			}
			echo '<br>'.HTML::anchor('/event', __('view_events'));
			?>
			
		  </div>
		</div>
		
		
			<div class="clearfix hidden-xs hidden-sm"></div>

<!--  Панель №6. Вывод результатов аналитики. 26.02.020 -->
		
	<div class="panel panel-danger">
	  <div class="panel-heading">
		<h3 class="panel-title"><?echo __('analyt_result', array('time_from'=>Date::formatted_time('-1 days', "d.m.Y H:i:s"), 'time_to'=>Date::formatted_time('now', "d.m.Y H:i:s")));?></h3>
	  </div>
	  <div class="panel-body">
		<?
		echo HTML::anchor('event/event_analyt', __('analyt_code_list'));
		if(isset ($analyt_result)){
		?>
					<table class="table table-striped table-hover table-condensed">

		<tr>
			
			<th><?echo __('ID_ANALIT');?></th>
			<th><?echo __('NAME_ANALYT');?></th>
			<th><?echo __('COUNT_EVENT_ANALYT');?></th>
			<th><?echo __('DETAIL');?></th>
			
		</tr>
		<?
			$config_analyt_code=Kohana::$config->load('artonitcity_config')->analit_err;
			//echo Debug::vars('133', $analyt_result);
			if(isset($analyt_result) and count($analyt_result))
			{
				foreach ($analyt_result as $key => $data)
				{
					$class_text='text-success';
					if(in_array(Arr::get($data, 'ANALIT'), $config_analyt_code)) $class_text='text-danger "font-weight-bold"';
					
					echo '<tr  class="'.$class_text.'">';
						echo '<td>'.Arr::get($data, 'ANALIT').'</td>';
						echo '<td>'.__(Arr::get($data, 'ANALIT').'a').'</td>';
						echo '<td>'.__(Arr::get($data, 'COUNT')).'</td>';
						echo '<td>'.HTML::anchor('event/event_analyt/'.Arr::get($data, 'ANALIT'),__('DETAIL')).'</td>';
					echo '</tr>';
					
				}
			}	else {
				echo __('no_data');
			}
			?>
			</table>
			<?
		} else {
			echo __('no data');
		}
			
		?>
	  </div>
	</div>

	</div>
	<p class="text-danger">
	<?echo __('Time execute ').$timeExecute.'<br>';?>
	</p>
</div>