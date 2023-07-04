<?php
if(!empty($data)) :
//--------------------------------------------------------

  $this->load->view("edit_form/parts/form_base_top", array('data' => $data));

  $attr = '';
  if(!empty($data['attr'])) {
    foreach($data['attr'] as $key => $attr_val) {
      if(!empty($attr)) $attr .= ' ';
      $attr .= $key . '="' . $attr_val . '"';
    }
  }
  
?>

<!--<div class="ui-select">-->
<div>
<?php 
    echo form_dropdown($data['key'] , $data['data'] , $data['value'] , $attr);
?>
</div>

<?php
  $this->load->view("edit_form/parts/form_base_btm", array('data' => $data));
//--------------------------------------------------------
endif;
?>
