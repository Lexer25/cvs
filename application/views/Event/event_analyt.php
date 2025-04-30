<script type="text/javascript">
      $(function () {
		var dateBegin = new Date();
		dateBegin.setHours(22, 0, 0, 0);
		dateBegin.setMonth(dateBegin.getMonth()+2);
	    //Инициализация datetimepicker1
        $("#datetimepicker1").datetimepicker(
		{language: 'ru', 
		showToday: true,
		sideBySide: true,
		defaultDate: dateBegin
		}
		);
      });

      $(document).ready(function() {
    	    $("#check_all3").click(function () {
    	         if (!$("#check_all3").is(":checked"))
    	            $(".checkbox").prop("checked",false);
    	        else
    	            $(".checkbox").prop("checked",true);
    	    });
    	});

 
  	$(function() {		
  		$("#tablesorter1").tablesorter({sortList:[[0,0]]});
  		$("#tablesorter2").tablesorter({sortList:[[0,0]], headers: { 0:{sorter: false}}});
  		
  	}
	
	
	);	
	
</script>
<div class="panel panel-primary">
	<div class="panel-heading"><h3 class="panel-title"><?echo __('event_analyt_title')?></h3></div>
	<div class="panel-body">
  
  
  
		<div class="panel panel-danger">
		  <div class="panel-heading">
			<h3 class="panel-title"><?php 
				echo HTML::anchor('event/event_analyt', 
					__($analyt_code.'a').
					$analyt_code.
					'<br>'.
					__('analyt_result', array('time_from'=>Date::formatted_time('-1 days', "d.m.Y H:i:s"), 'time_to'=>Date::formatted_time('now', "d.m.Y H:i:s")))
					)
					;
				
				?></h3>
		  </div>
		  <div class="panel-body">
			<?php echo __($analyt_code.'a');?>
		  </div>
	  </div>
	  
	  <div class="form-group">
		<? 
		$res='';
		if(isset($analyt_result) and count($analyt_result))
		{
			//echo Debug::vars('45', $analyt_result_door); exit;
			?>
		<div class="panel panel-primary" id="refresh">
			<div class="panel-heading"><?echo __('device_code_analyt', array('analyt_code'=>$analyt_code, 'time_from'=>Date::formatted_time('-1 days', "d.m.Y H:i:s"), 'time_to'=>Date::formatted_time('now', "d.m.Y H:i:s")));?></div>
			<div class="panel-body">
				
				<table id="tablesorter1" class=" table table-striped table-hover table-condensed tablesorter">
					<thead>		
						<tr>
								<th><?php echo __('ID_CONTROLLER');?></th>
								<th><?php echo __('DEVICE');?></th>
								<th><?php echo __('ID_DEV');?></th>
								<th><?php echo __('NAME');?></th>
								<th><?php echo __('COUNT');?></th>
								<th><?php echo __('DEVICE_VERSION');?></th>
								<th><?php echo __('NOTE');?></th>
								
								
								
						</tr>
						<tr align="center">
							<td>1</td>
							<td>2</td>
							<td>3</td>
							<td>4</td>
							<td>5</td>
							<td>6</td>
							<td>7</td>
							
						</tr>
					</thead>
					<tbody>
						<?
						if(isset($analyt_result_door))
						{
							foreach ($analyt_result_door as $key=>$value)
							{
								echo '<tr>';
									echo '<td>'.Arr::get($value, 'ID_DEV', 'no').'</td>';//1
									echo '<td>'.Arr::get($value, 'NAME_DEV', 'no').'</td>';//2
									echo '<td>'.Arr::get($value, 'ID_DOOR', 'no').'</td>';//3
									echo '<td>'.Arr::get($value, 'NAME_DOOR', 'no').'</td>';//4
									echo '<td>'.Arr::get($value, 'COUNT', 'no').'</td>';//5
									echo '<td>'.Arr::get($value, 'VERSION', 'no').'</td>';//6
									echo '<td>'.Arr::get($value, 'PARAM_DEV', 'no').'<br>'.Arr::get($value, 'PARAM_DOOR', 'no').'</td>';//6
								echo '</tr>';
							}
						}
						?>
					<tbody>
				</table>
			</div>
		</div>
			
		<div class="panel panel-primary" id="refresh">
			<div class="panel-heading"><?echo __('event_code_analyt', array('analyt_code'=>$analyt_code, 'time_from'=>Date::formatted_time('-1 days', "d.m.Y H:i:s"), 'time_to'=>Date::formatted_time('now', "d.m.Y H:i:s")));?></div>
				<div class="panel-body">
		
				<table id="tablesorter2" class=" table table-striped table-hover table-condensed tablesorter">
					<thead>		
						<tr>
								<th><?php echo __('NAME_EVENT');?></th>
								<th><?php echo __('NAME');?></th>
								<th><?php echo __('DEVICE');?></th>
								<th><?php echo __('PEOPLE');?></th>
								<th><?php echo __('event_count_24');?></th>
								<th><?php echo __('DEVICEVERSION');?></th>
								<th><?php if($analyt_code == 657) echo __('recommendation');?></th>
								
								
						</tr>
						<tr align="center">
							<td>1</td>
							<td>2</td>
							<td>3</td>
							<td>4</td>
							<td>5</td>
							<td>6</td>
							<td>7</td>
						</tr>
					</thead>
					<?
					foreach ($analyt_result as $key=>$value)
					{
						echo '<tr>';
							echo '<td>'.$value['EVENT_NAME'].'</td>';//1
							echo '<td>'.HTML::anchor('door/doorInfo/'.$value['ID_DEV'],$value['NAME'].' (id_dev='.$value['ID_DEV'].')').'</td>';//2
							echo '<td>'.__('event_analyt_3', array( 
										'e_DEVICENAME' => Arr::get($value, 'DEVICENAME'),
										'e_id_dev' => Arr::get($value, 'DEVICEID'),
										'e_serv' =>  Arr::get($value, 'SRV_NAME'),
										)).'</td>';//3
							echo '<td>'.HTML::anchor('people/peopleInfo/'.$value['ESS1'].'/'.Arr::get($value, 'ID_CARD', 'no'), //4
								Arr::get($value, 'NOTE').'<br>'.
								__('view_event_analit_657', array(
									'e_id_reader'=> Arr::get($value, 'ID_READER', 'no'),
									'e_id_pep'=> Arr::get($value, 'ESS1', 'no'),
									'e_key'=> Arr::get($value, 'ID_CARD', 'no'),
									'e_DEVIDX'=> Arr::get($value, 'DEVIDX', 'no'),
									'e_LOAD_TIME'=> Arr::get($value, 'LOAD_TIME', 'no'),
									'e_LOAD_RESULT'=> Arr::get($value, 'LOAD_RESULT', 'no'),								
								)))
								.'</td>';//4
							echo '<td>'.Arr::get($value, 'COUNT', 'no field').'</td>';//5
							echo '<td>'.Arr::get($value, 'DEVICEVERSION', 'no field').'</td>';//6
							if($analyt_code == 657) 
								echo '<td>'.
								//Debug::vars('87', $value).
								__('checkConfig'.Arr::get($value, 'checkConfig', 'no field'), 
									array(
										'checkConfig'=>Arr::get($value, 'checkConfig', 'no field'),
										'TESTMODE'=>Arr::get($value, 'TESTMODE', 'no field'),
										'singleListDevice'=>Arr::get($value, 'singleListDevice', 'no field'),
										'singleListDB'=>Arr::get($value, 'singleListDB', 'no field'),
										)
									).'</td>';//7
							
						echo '</tr>';
					}
				
		}
					?>
				</table>				

			</div>	
		</div>	
	
  
	</div>
</div>