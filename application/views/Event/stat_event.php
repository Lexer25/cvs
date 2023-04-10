<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title"><?echo __('event_panel_title')?></h3>
  </div>
  <div class="panel-body">
  
  
  <?
  //echo Debug::vars('9', $list);
  echo __('event_panel_desc');?>
  <div class="form-group">
	<? 
	$res='';
	foreach ($event_stat as $key=>$value)
	{
		$res .=__('', array(
			':name'	=> $value['NAME_EVENT'], 
			':count'	=> $value['COUNT_EVENT'], 
			));
	}
	return $res;
	?>
				

</div>	
	
  
</div>
</div>