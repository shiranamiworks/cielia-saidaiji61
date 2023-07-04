<?php
if(!empty($data)) :
//--------------------------------------------------------
  $tab_pos_class = 'nav-tabs-top';
  $tab_style = 'style="margin-bottom:50px"';
  if(!empty($tab_position) && $tab_position == 'bottom') {
    $tab_pos_class = 'nav-tabs-bottom';
    $tab_style = '';
  }
?>
<ul class="nav nav-tabs <?=$tab_pos_class?>" <?=$tab_style?>>
<?php
  $cnt = 1;
  foreach($data as $tab) : 
    $active_class = '';
    if($cnt == 1) $active_class = ' class="active"';
    if(!empty($tab['dropdown'])) :
    //dropdown menu
    //--------------------------------------------------------
?>
      <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?=$tab['name']?> <b class="caret"></b></a>
      <ul class="dropdown-menu">
<?php
      foreach($tab['dropdown'] as $dropdown_tab) :
?>
        <li><a data-toggle="tab" href="#content<?=$dropdown_tab['id']?>"><?=$dropdown_tab['name']?></a></li>
<?php
      endforeach;
?>
      </ul>
<?php
    else :
    //--------------------------------------------------------
?>
  <li<?=$active_class?>><a data-toggle="tab" href="#content<?=$tab['id']?>"><?=$tab['name']?></a></li>
<?php
    endif;
    
    $cnt++;
//--------------------------------------------------------
  endforeach;
?>
</ul>
<?php
//--------------------------------------------------------
endif;
?>
