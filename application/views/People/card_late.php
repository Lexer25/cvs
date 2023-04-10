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
  		$("#tablesorter-demo").tablesorter({sortList:[[0,0]], widgets: ['zebra'], headers: { 0:{sorter: false}}});
  	});	
  	
</script> 


<div class="panel panel-primary  ">
  <div class="panel-heading">
    <h3 class="panel-title"><?echo __($title)?></h3>
  </div>
  <div class="panel-body">
	
	<?echo __('total_count').' ';
		echo isset($list)? count($list) : '0';?>	
	
	<?echo Form::open('people/card_late_save_to_file');?>
		<button type="submit" class="btn btn-primary" name="card_late_save_to_file"  value="1"><?echo __('card_late_save_to_file')?></button>
	<?echo Form::close();?>
	
	
	
	<?echo Form::open('people/people_delete', array('class'=>'form-inline'));?>
		
		
		
		 
		<table id="tablesorter-demo" class="tablesorter">
		
		<thead>
		<tr>
			<th><?echo __('pp');?></th>
			<th>		
				<label><input type="checkbox" name="id_pep" id="check_all3"> Выделить всё</label>
			</th>
			<th><?echo __('pep_id');?></th>
			<th><?echo __('name');?></th>
			<th><?echo __('org_name');?></th>
			<th><?echo __('note');?></th>
			<th><?echo __('card');?></th>
			<th><?echo __('card_date_end');?></th>
			<th><?echo __('overlate');?></th>
			<th><?echo __('isactive');?></th>
			
		</tr>
		</thead>
		<tbody>
		<?
		$pp=0;
		foreach ($list as $key=>$contact)
		{
		echo '<tr>';
		echo '<td>'.$pp++.'</td>';
		echo '<td><label>'.Form::checkbox('id_pep[]', Arr::get($contact, 'ID_PEP'), FALSE, array('class'=>'checkbox')).'</label></td>';
					echo '<td>'.Arr::get($contact, 'ID_PEP').'</td>';
				echo '<td>'.HTML::anchor('people/peopleInfo/'.Arr::get($contact, 'ID_PEP'),  Arr::get($contact,'SURNAME').' '.Arr::get($contact, 'NAME').' '.Arr::get($contact,'PATRONYMIC')).'<br>';
				echo '<td>'.Arr::get($contact, 'ORG_PARENT', __('No_card')); // .'/'. Arr::get($contact, 'ORG_NAME', __('No_card')).'<br>';
				echo '<td>'.Arr::get($contact, 'NOTE', __('No_card')).'<br>';
				echo '<td>'.Arr::get($contact, 'ID_CARD', __('No_card')).'<br>';
				echo '<td>'.date("d.m.Y", strtotime(Arr::get($contact, 'TIMEEND', __('No_card')))).'<br>';
				//$overlate=strtotime('now') - strtotime(Arr::get($contact, 'TIMEEND'));
				$overlate = Date::span(strtotime(Arr::get($contact, 'TIMEEND')), strtotime('now'), 'months,days');
				//echo '<td>'. round($overlate/3600/24).'<br>';
				echo '<td>'. Arr::get($overlate, 'months').' мес. '.Arr::get($overlate, 'days').' дн.<br>';
				echo '<td>'. Arr::get($contact, 'ISACTIVE',0).'<br>';
				
			echo '</tr>';
					
			}
				?>
		</tbody>
	</table>
	
	<!-- Навигация -->
<nav class="navbar navbar-default navbar-fixed-bottom disable" role="navigation">
  <div class="container">
  <div class="row">
  
	<!-- Инициализация виджета "Bootstrap datetimepicker" --> 
		
		<div class="form-group">
		  <div class="input-group date" id="datetimepicker1">
			<input type="text" class="form-control" name="timeTo" >
			<span class="input-group-addon">
			  <span class="glyphicon glyphicon-calendar"></span>
			</span>
		  </div>
		</div>


	<button 
	  	type="submit" 
	  	class="btn btn-warning" 
	  	name="people_long"  
	  	value="1" 
	  	<?php if(!Auth::instance()->logged_in()) echo 'disabled'?> onclick="return confirm('<?echo __('people_long_alert')?>') ? true : false;"><?echo __('people_long')?>
	 </button>


	<button 
		  	type="submit" 
		  	class="btn btn-success" 
		  	name="people_unactive"  
		  	value="1" 
		  	<?php if(!Auth::instance()->logged_in()) echo 'disabled'?>
		  	onclick="return confirm('<?echo __('people_unactive_alert')?>') ? true : false;"><?echo __('people_unactive')?>
	</button>
  	  
  	<button type="submit" 
			class="btn btn-danger pull-right" 
			name="card_delete"  
			value="1" 
			<?php if(!Auth::instance()->logged_in()) echo 'disabled'?> onclick="return confirm('<?echo __('people_delete_alert')?>') ? true : false;"><?echo __('card_delete')?>
	</button>
	
	</div>
	</div>
</nav>	
		
<?echo Form::close();?>	
</div>	
</div>
 
    
