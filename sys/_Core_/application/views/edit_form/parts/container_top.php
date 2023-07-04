<?php
if(empty($tab_key)) {
  $tab_key = 'Main';
}
$tab_active_class = '';
if(!empty($tab_active)) {
  $tab_active_class = ' in active';
}
?>

<div class="tab-pane fade<?=$tab_active_class?>" id="content<?=$tab_key?>">

