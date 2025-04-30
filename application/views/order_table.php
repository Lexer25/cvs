<!-- Управление очередью загрузки карт в контроллеры -->
<script type="text/javascript">
      $(document).ready(function() {
    	    $("#check_all1").click(function () {
    	         if (!$("#check_all1").is(":checked"))
    	            $(".checkbox1").prop("checked",false);
    	        else
    	            $(".checkbox1").prop("checked",true);
    	    });
    	});

      $(document).ready(function() {
  	    $("#check_all2").click(function () {
  	         if (!$("#check_all2").is(":checked"))
  	            $(".checkbox2").prop("checked",false);
  	        else
  	            $(".checkbox2").prop("checked",true);
  	    });
  	});
    


  	$(function() {		
		$("#table15").tablesorter({sortList:[[0,0],[2,1]], widgets: ['zebra']});
		$("#options").tablesorter({sortList: [[0,0]], headers: { 0:{sorter: false}}});
	});	

  	$(function() {		
  		$("#table1").tablesorter({sortList:[[0,0]], headers: { 0:{sorter: false}}});
  	});

  	$(function() {		
  		$("#table2").tablesorter({sortList:[[0,0]], headers: { 0:{sorter: false}}});
  	});	
  	
	$(function() {		
		$("#table12").tablesorter({sortList:[[0,0],[2,1]], widgets: ['zebra']});
		$("#options").tablesorter({sortList: [[0,0]], headers: { 0:{sorter: false}}});
	});	
  	
	//setInterval(function() { $("#refresh").load(location.href+" #refresh>*","");}, 5000);
</script> 
<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title"><?echo __('Load_panel_title')?></h3>
	</div>
	<?echo Form::open('Dashboard/load_order');?>
	<div class="panel-body">
  
		<div class="panel panel-primary" id="refresh">
			<div class="panel-heading"><?echo __('loading_card');?></div>
			<div class="panel-body">
				<?echo __('loading_card');?>
						<!-- <table class="table table-striped table-hover table-condensed">  -->
						<table id="table1" class=" table table-striped table-hover table-condensed tablesorter">
						<thead>
							<tr>
								<th>
									<label><input type="checkbox" name="stop_load" id="check_all1"> Выделить всё</label>
								</th>
								<th><?echo __('ID_DEV');?></th>
								<th><?echo __('SERVER');?></th>
								<th><?echo __('DEVICE');?></th>
								<th><?echo __('NAME');?></th>
								<th><?echo __('CARD_FOR_LOAD');?></th>
								<th><?echo __('CARD_FOR_DELETE');?></th>
							</tr>
						</thead>
						<tbody>
							<? 
							$count_write=0;
							$count_delete=0;
							foreach ($list as $key => $value)
							{
								echo '<tr class="'.Arr::get($value, 'TR_COLOR', 'active').'">';
								echo '<td><label>'.Form::checkbox('stop_load['.Arr::get($value, 'ID_DEV').']', 1, FALSE, array('class'=>'checkbox1')).'</label></td>';
									echo '<td>'.Arr::get($value, 'ID_DEV', 'No data').'</td>';
									echo '<td>'.Arr::get($value, 'SERVER', 'No data').'</td>';
									echo '<td>'.Arr::get($value, 'DEVICE', 'No data').'</td>';
									echo '<td>'.HTML::anchor('door/doorInfo/'.Arr::get($value, 'ID_DEV'), Arr::get($value, 'NAME', 'No data')).'</td>';
									echo '<td>'.Arr::get($value, 'COUNT_WRITE', '-').'</td>';
									echo '<td>'.Arr::get($value, 'COUNT_DELETE', '-').'</td>';
									$count_write=$count_write+Arr::get($value, 'COUNT_WRITE', 0);
									$count_delete=$count_delete+Arr::get($value, 'COUNT_DELETE', 0);
								echo '</tr>';
								
							}
							?>
							
							<tr>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td><?echo $count_write;?></td>
								<td><?echo $count_delete;?></td>
							</tr>
							<tr>
								<td></td>
								<td><?echo __('total')?></td>
								<td><?echo $count_write+$count_delete;?></td>
								<td></td>
								<td></td>
							</tr>
					</tbody>
						</table>

					<button type="submit" class="btn btn-primary" >Остановить загрузку</button>
				
			</div>
		</div>
			
		<div class="panel panel-primary">
			<div class="panel-heading"><?echo __('overcount_card');?></div>
			<div class="panel-body">
				<?echo __('overcount_card');?>
				

					<!-- <table class="table table-striped table-hover table-condensed">  -->
					<table id="table2" class=" table table-striped table-hover table-condensed tablesorter">
					<thead>
					<tr>
							<th>
									<label><input type="checkbox" name="reload" id="check_all2"> Выделить всё</label>
								</th>

							<th><?echo __('ID_DEV');?></th>
							<th><?echo __('SERVER');?></th>
							<th><?echo __('DEVICE');?></th>
							<th><?echo __('NAME');?></th>
							<th><?echo __('CARD_FOR_LOAD');?></th>
							<th><?echo __('CARD_FOR_DELETE');?></th>
						</tr>
					</thead>
					<tbody>
						<? 
						$count_write=0;
						$count_delete=0;
						foreach ($overcount as $key => $value)
						{
							echo '<tr class="'.Arr::get($value, 'TR_COLOR', 'active').'">';
							echo '<td><label>'.Form::checkbox('reload['.Arr::get($value, 'ID_DEV').']', 1, FALSE, array('class'=>'checkbox2')).'</label></td>';
								echo '<td>'.Arr::get($value, 'ID_DEV', 'No data').'</td>';
								echo '<td>'.Arr::get($value, 'SERVER', 'No data').'</td>';
								echo '<td>'.Arr::get($value, 'DEVICE', 'No data').'</td>';
								echo '<td>'.HTML::anchor('door/doorInfo/'.Arr::get($value, 'ID_DEV'), Arr::get($value, 'NAME', 'No data')).'</td>';
								echo '<td>'.Arr::get($value, 'COUNT_WRITE', '-').'</td>';
								echo '<td>'.Arr::get($value, 'COUNT_DELETE', '-').'</td>';
								$count_write=$count_write+Arr::get($value, 'COUNT_WRITE', 0);
								$count_delete=$count_delete+Arr::get($value, 'COUNT_DELETE', 0);
							echo '</tr>';
							
						}
						?>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td><?echo $count_write;?></td>
							<td><?echo $count_delete;?></td>
						</tr>
						<tr>
							<td></td>
							<td><?echo __('total')?></td>
							<td><?echo $count_write+$count_delete;?></td>
							<td></td>
							<td></td>
						</tr>
					</tbody>
					</table>

				 <!-- <button type="submit" name="reload_butt" class="btn btn-primary" >Загрузить</button> -->
				 <!-- <button type="submit" name="del_queue" class="btn btn-warning" >Удалить очередь</button> -->
				 <?
				 echo Form::submit('reload_butt','Загрузить', array('class'=>'btn btn-primary', 'onclick'=>'return confirm(\''.__('reload_butt_mess').'\') ? true : false;'));
				 echo Form::submit('del_queue','Удалить очередь', array('class'=>'btn btn-warning', 'onclick'=>'return confirm(\''.__('del_queue_mess').'\') ? true : false;'));
				 ?>
			</div>
		</div>
	</div>	
	<?echo Form::close();?>
	
  
</div>
