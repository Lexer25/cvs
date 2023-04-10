<?php 
	//echo Debug::vars('11',$list);
?>
		
<div class="panel panel-primary">
  <div class="panel-heading ">
    <h3 class="panel-title"><?echo __('people_panel_title')?></h3>
  </div>
  <div class="panel-body">
	

		<table class="table table-striped table-hover table-condensed table-bordered">
		<tr>
			<th><?echo __('pep_id');?></th>
			<th><?echo __('name');?></th>
			<th><?echo __('org_name');?></th>
			<th><?echo __('card');?></th>
			<th><?echo __('card_type');?></th>
			<th><?echo __('about_pep_authmode');?></th>
			<th><?echo __('last_event');?></th>
		</tr>
		
		<?foreach ($list as $key=>$contact)
		{
		echo '<tr>';
			echo '<td>'.Arr::get($contact, 'ID_PEP').'</td>';
			/*
			echo '<td>';
			if (Arr::get($contact, 'PHOTO') != null) { ?>
				<img src="data:image/jpeg;base64,<?php echo base64_encode($contact['PHOTO']); ?>" height="200" alt="photo" />
				<?php } else { 
					echo HTML::image("images/nophoto.png", array('height' => 200, 'alt' => 'photo'));
			}
			echo '</td>';
			*/
				echo '<td>'.HTML::anchor('people/peopleInfo/'.Arr::get($contact, 'ID_PEP').'/'.Arr::get($contact, 'ID_CARD', __('No_card')),  Arr::get($contact,'SURNAME').' '.Arr::get($contact, 'NAME').' '.Arr::get($contact,'PATRONYMIC')).'<br>';
				echo '<td>'.Arr::get($contact, 'ORG_NAME', __('No_card')).'<br>';
				echo '<td>'.Arr::get($contact, 'ID_CARD', __('No_card')).'<br>';
				echo '<td>'.Arr::get($contact, 'CARDTYPENAME', __('No_cardtype')).'<br>';
				echo '<td>'.Model::factory('stat')->Authmode(Arr::get($contact, 'AUTHMODE', 0)).'<br>';
				echo '<td>'.Arr::get($contact, 'MAX', __('No_event')).'<br>';
			echo '</tr>';
					
			}
				?>
			
	</table>
		
	

</div>	
	
  
</div>
