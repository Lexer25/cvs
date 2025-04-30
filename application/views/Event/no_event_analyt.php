<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title"><?echo __('event_analyt_title')?></h3>
  </div>
  <div class="panel-body">
		<div class="panel panel-danger">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo Kohana::message('analyt_code', 'panel.title', 'no_data');?></h3>
		</div>
		  <div class="panel-body">
			<?php 
				echo Kohana::message('analyt_code', 'panel.desc_analyt1', 'no_data').'<br>';
				echo Kohana::message('analyt_code', 'panel.desc_analyt2', 'no_data').'<br>';
			?>
			<table id="table1" class=" table table-striped table-hover table-condensed tablesorter">
			<thead>
				<tr>
					<th><?php echo __('SER_NUM');?></th>
					<th><?echo Kohana::message('analyt_code', 'table_analyt_th.colom11', 'no_data');?></th>
					<th><?echo Kohana::message('analyt_code', 'table_analyt_th.colom12', 'no_data');?></th>
				</tr>
			</thead>
				<tr>
					<td>1</td>
					<td><?php echo Kohana::message('analyt_code', 'table_analyt_th.colom2', 'no_data');?></td>
					<td><?php echo Kohana::message('analyt_code', 'table_analyt_th.desc2', 'no_data');?></td>
				</tr>
				<tr>
					<td>2</td>
					<td><?php echo Kohana::message('analyt_code', 'table_analyt_th.colom3', 'no_data');?></td>
					<td><?php echo Kohana::message('analyt_code', 'table_analyt_th.desc3', 'no_data');?></td>
					
				</tr>
				<tr>
					<td>3</td>
					<td><?php echo Kohana::message('analyt_code', 'table_analyt_th.colom4', 'no_data');?></td>
					<td><?php echo Kohana::message('analyt_code', 'table_analyt_th.desc4', 'no_data');?></td>
				</tr>
				<tr>
					<td>4</td>
					<td><?php echo Kohana::message('analyt_code', 'table_analyt_th.colom5', 'no_data');?></td>
					<td><?php echo Kohana::message('analyt_code', 'table_analyt_th.desc5', 'no_data');?></td>
				</tr>
				<tr>
					<td>5</td>
					<td><?php echo Kohana::message('analyt_code', 'table_analyt_th.colom6', 'no_data');?></td>
					<td><?php echo Kohana::message('analyt_code', 'table_analyt_th.desc6', 'no_data');?></td>
				</tr>
				<tr>
					<td>6</td>
					<td><?php echo Kohana::message('analyt_code', 'table_analyt_th.colom7', 'no_data');?></td>
					<td><?php echo Kohana::message('analyt_code', 'table_analyt_th.desc7', 'no_data');?></td>
				</tr>
<tr>
					<td>7</td>
					<td><?php echo Kohana::message('analyt_code', 'table_analyt_th.colom8', 'no_data');?></td>
					<td><?php echo Kohana::message('analyt_code', 'table_analyt_th.desc8', 'no_data');?></td>
				</tr>

				
				
				
				
				
			</table>
		  </div>
	  </div>
  
  <?
  //echo Debug::vars('9', $list);// exit;
  echo __('No data for analyt_code', array('analyt_code' => $analyt_code)) . ' '.HTML::mailto('support@artonit.ru', 'Необходима страница анализа кода '.$analyt_code).'.';?>
  <div class="form-group">
				
	<table id="table1" class=" table table-striped table-hover table-condensed tablesorter">
	<thead>
		<tr>
			<th><?echo Kohana::message('analyt_code', 'table_analyt_th.colom1', 'no_data');?></th>
			<th><?echo Kohana::message('analyt_code', 'table_analyt_th.colom11', 'no_data');?></th>
			<th><?echo Kohana::message('analyt_code', 'table_analyt_th.colom2', 'no_data');?></th>
			<th><?echo Kohana::message('analyt_code', 'table_analyt_th.colom3', 'no_data');?></th>
			<th><?echo Kohana::message('analyt_code', 'table_analyt_th.colom4', 'no_data');?></th>
			<th><?echo Kohana::message('analyt_code', 'table_analyt_th.colom5', 'no_data');?></th>
			<th><?echo Kohana::message('analyt_code', 'table_analyt_th.colom6', 'no_data');?></th>
			<th><?echo Kohana::message('analyt_code', 'table_analyt_th.colom7', 'no_data');?></th>
			<th><?echo Kohana::message('analyt_code', 'table_analyt_th.colom8', 'no_data');?></th>
			<th><?echo Kohana::message('analyt_code', 'table_analyt_th.colom9', 'no_data');?></th>
			<th><?echo Kohana::message('analyt_code', 'table_analyt_th.colom10', 'no_data');?></th>
			
						
		</tr>
	</thead>
	<tbody>
		<? 
		
				
		
		//echo Debug::vars('35', Kohana::message('analyt_code'));

		foreach (Kohana::message('analyt_code')	 as $key=>$value)
		{
			if($key >= 400) 
			{				
			if(Kohana::message('analyt_code',$key.'.analit_code') == 0) $tr_class="warning";
			if(Kohana::message('analyt_code',$key.'.analit_code') == 1) $tr_class="success";
			if(Kohana::message('analyt_code',$key.'.analit_code') == 2) $tr_class="danger";
			if(Kohana::message('analyt_code',$key.'.analit_code') == 3) $tr_class="active";
			echo '<tr class="'.$tr_class.'">';

				echo '<td>'.Kohana::message('analyt_code', $key.'.code', 'no_data').'</td>';
				echo '<td>'.Kohana::message('analyt_code', 'error_class.'.(Kohana::message('analyt_code',$key.'.analit_code')), 'no_data').'</td>';
				echo '<td>'.(Kohana::message('analyt_code', $key.'.people_is_active', 0) ? 'Да' : 'Нет') .'</td>';
				echo '<td>'.(Kohana::message('analyt_code', $key.'.card_is_active',  0) ? 'Да' : 'Нет') .'</td>';
				echo '<td>'.(Kohana::message('analyt_code', $key.'.pass_is_valide',  0) ? 'Да' : 'Нет') .'</td>';
				echo '<td>'.(Kohana::message('analyt_code', $key.'.order_for_load',  0) ? 'Да' : 'Нет') .'</td>';
				echo '<td>'.(Kohana::message('analyt_code', $key.'.order_for_delete',  0) ? 'Да' : 'Нет') .'</td>';
				echo '<td>'.(Kohana::message('analyt_code', $key.'.single_list',  0) ? 'Да' : 'Нет') .'</td>';
				echo '<td>'.(Kohana::message('analyt_code', $key.'.test_mode_is_ON',  0) ? 'Да' : 'Нет') .'</td>';
				echo '<td>'.Kohana::message('analyt_code', $key.'.desc', 'no_data').'</td>';
				echo '<td>'.Kohana::message('analyt_code', $key.'.recommendation', 'no_data').'</td>';
				
			echo '</tr>';
			}
		}
				
		?>

	</tbody>
	</table>
</div>	
	
  
</div>
</div>