 <script type="text/javascript">

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
    <h3 class="panel-title"><?echo __('people_without_card')?></h3>
  </div>
  <div class="panel-body">
	
	<?echo __('total_count').' ';
		echo isset($list)? count($list) : '0';?>	
	

	<?echo Form::open('people/people_without_card_delete', array('class'=>'form-inline'));?>
		
		
		
		<!-- <table class="table table-striped table-hover table-condensed table-bordered" id="example"> -->
		
		 
		<table id="tablesorter-demo" class="tablesorter">
		
		<thead>
		<tr>
			<th>		
				<label><input type="checkbox" name="id_pep" id="check_all3"> Выделить всё</label>
			</th>
			<th><?echo __('pep_id');?></th>
			<th><?echo __('name');?></th>
			<th><?echo __('org_name');?></th>
			<th><?echo __('note');?></th>
			
			<th><?echo __('isactive');?></th>
			
		</tr>
		</thead>
		<tbody>
		<?foreach ($list as $key=>$contact)
		{
		echo '<tr>';
		if (Arr::get($contact, 'ID_PEP') == 1){
			echo '<td><label>'.Form::checkbox('id_pep[]', Arr::get($contact, 'ID_PEP'), FALSE, array('class'=>'checkbox', 'disabled'=>'disabled')).'</label></td>';
		} else {
			echo '<td><label>'.Form::checkbox('id_pep[]', Arr::get($contact, 'ID_PEP'), FALSE, array('class'=>'checkbox')).'</label></td>';
		}
			echo '<td>'.Arr::get($contact, 'ID_PEP').'</td>';
			echo '<td>'.HTML::anchor('people/peopleInfo/'.Arr::get($contact, 'ID_PEP'),  Arr::get($contact,'SURNAME').' '.Arr::get($contact, 'NAME').' '.Arr::get($contact,'PATRONYMIC')).'<br>';
			echo '<td>'.Arr::get($contact, 'ORG_NAME', __('No_org_name')); // .'/'. Arr::get($contact, 'ORG_NAME', __('No_card')).'<br>';
			echo '<td>'.Arr::get($contact, 'NOTE', __('No_note')).'<br>';
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
  



	<button 
	  	type="submit" 
	  	class="btn btn-warning" 
	  	name="people_long"  
	  	value="1" 
	  	<?php //if(!Auth::instance()->logged_in()) echo 'disabled'?>
	  	onclick="return confirm('<?echo __('people_without_card_delete')?>') ? true : false;"><?echo __('delete')?>
	 </button>


	
	
	</div>
	</div>
</nav>	
		
<?echo Form::close();?>	
</div>	
</div>
 
    
