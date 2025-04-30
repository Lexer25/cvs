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

<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title"><?echo __('card_late_next_week_info')?></h3>
  </div>
  <div class="panel-body">
		

	<?echo __('total_count');?>
	<?echo isset($list)? count($list) : '0';?>	
	
	<?echo Form::open('people/people_delete', array('class'=>'form-inline'));?>
	
		<!-- <table class="table table-striped table-hover table-condensed table-bordered">  -->
		<table id="tablesorter-demo" class="tablesorter">
		<thead>
		<tr>
			<th>		
				<label><input type="checkbox" name="id_pep" id="check_all3"> Выделить всё</label>
			</th>
			<th><?echo __('pep_id');?></th>
			<th><?echo __('name');?></th>
			<th><?echo __('org_name');?></th>
			<th><?echo __('card');?></th>
			<th><?echo __('card_date_start');?></th>
			<th><?echo __('card_date_end');?></th>
			
		</tr>
		<thead>
		<tbody>
		<?foreach ($list as $key=>$contact)
		{
		echo '<tr>';
		echo '<td><label>'.Form::checkbox('id_pep[]', Arr::get($contact, 'ID_PEP'), FALSE, array('class'=>'checkbox', 'disabled'=>'disabled')).'</label></td>';
			echo '<td>'.Arr::get($contact, 'ID_PEP').'</td>';
			echo '<td>'.HTML::anchor('people/peopleInfo/'.Arr::get($contact, 'ID_PEP'),  Arr::get($contact,'SURNAME').' '.Arr::get($contact, 'NAME').' '.Arr::get($contact,'PATRONYMIC')).'</td>';
			echo '<td>'.Arr::get($contact, 'ORG_PARENT', __('...')).'/'.Arr::get($contact, 'ORG_NAME', __('No_org_name')).'</td>';
			echo '<td>'.Arr::get($contact, 'ID_CARD', __('No_card')).'</td>';
			echo '<td>'; 
			if (Arr::get($contact, 'TIMESTART') !== NULL) {
				 echo date("d.m.Y H:i", strtotime(Arr::get($contact, 'TIMESTART')));
			} else {
				echo 'n/a';
			}
				echo '</td>';
				
					echo '<td>';
					if (Arr::get($contact, 'TIMEEND') !== NULL) {
						echo date("d.m.Y H:i", strtotime(Arr::get($contact, 'TIMEEND')));
					} else {
						echo 'n/a';
					}
					echo '</td>';
					
				
				
			echo '</tr>';
					
			}
				?>
		</tbody>	
	</table>

<?echo Form::close();?>		

</div>	
	
  
</div>
