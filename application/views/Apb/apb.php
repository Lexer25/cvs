<? //http://itchief.ru/lessons/bootstrap-3/30-bootstrap-3-tables;
// страница отображения данных по парковочной системе
echo Form::open('apb/apb_control');
?>

<?php

		$e_mess=Validation::Factory(Session::instance()->as_array())
				->rule('e_mess','is_array')
				->rule('e_mess','not_empty')
				;
		
		if($e_mess->check())
		{
	
			$param='Yes message<br>';
			
			foreach(Arr::get($e_mess, 'e_mess') as $key=>$value)
			{
				$param.=$value.'<br>';
			}
			?>
			<div id="my-alert" class="alert alert-danger alert-dismissible" role="alert">
				<?php 
					echo $param;
				?>
				
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<?php
			
		} else 
		{
			
			
		}
		Session::instance()->delete('e_mess');
?>


			
			
<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title"><?echo Kohana::message('apb','apb_list')?></h3>
	</div>
	<div class="panel-body">
		<?php
			echo Kohana::message('apb','apb_list_desc');
			//echo Debug::vars('11', $config);
		?>
<table class="table table-striped table-hover table-condensed">


		<tr>
			<th><?echo Kohana::message('apb','select','select');?></th>
			<th><?echo Kohana::message('apb','id_apb');?></th>
			<th><?echo Kohana::message('apb','apb_name');?></th>
			<th><?echo Kohana::message('apb','apb_duration');?></th>
			<th><?echo Kohana::message('apb','apb_enabled');?></th>
			
			
		</tr>
		<?php 
		$i=0;
		$checked='no';
		foreach($apb_list as $key=>$value)
		{
			echo '<tr>';
				if($i==0) echo '<td>'.Form::radio('id_apb', Arr::get($value,'ID'), FALSE, array('checked'=>$checked)).'</td>';
				if($i>0) echo '<td>'.Form::radio('id_apb', Arr::get($value,'ID'), FALSE).'</td>';
				//echo '<td>'.$value['ID'].'</td>';
				echo '<td>'.Arr::get($value,'ID').'</td>';
				echo '<td>'.Arr::get($value,'NAME').'</td>';
				echo '<td>'.Arr::get($value,'DURATION').'</td>';
				echo '<td>'.Arr::get($value,'ENABLED').'</td>';
			echo '</tr>';	
			$i++;
		}
		
		?>
	</table>		
			
		
		<?php
			echo Form::button('todo', Kohana::message('apb','apb_edit'), array('value'=>'edit_apb','class'=>'btn btn-success', 'type' => 'submit'));	
			echo Form::button('todo', Kohana::message('apb','apb_del'), array('value'=>'del_apb','class'=>'btn btn-success', 'type' => 'submit'));
		?>
</div>
</div>




<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title"><?echo Kohana::message('apb','apb_add_apb','apb_add_apb')?></h3>
  </div>
  <div class="panel-body">
  
    <?
	echo Kohana::message('apb','apb_add_new_apb');
	echo Form::input('add_apb', 'Новый периметр');
	
	
		
	echo Form::button('todo', Kohana::message('apb','apb_add','apb_add'), array('value'=>'add_apb','class'=>'btn btn-success', 'type' => 'submit'));	
	?>	

  </div>

</div>
  <?echo Form::close();?>
<script>
    $(function(){
        window.setTimeout(function(){
            $('#my-alert').alert('close');
        },5000);
    });
</script>
  