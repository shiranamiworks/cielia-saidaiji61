<?php
if(!empty($data)) :
//--------------------------------------------------------
  
  $this->load->view("edit_form/parts/form_base_top", array('data' => $data));
  
  $attr = '';
  if(!empty($data['attr'])) {
    foreach($data['attr'] as $key => $attr_val) {
      $attr .= ' ' . $key . '="' . $attr_val . '"';
    }
  }
?>

<input type="hidden" name="<?=$data['key']?>" value="">
<?php
if(!empty($data['data'])) :
  foreach($data['data'] as $item_key => $item_value) :
    $checked = '';
    if(!empty($data['value']) && $item_key == $data['value']) {
      $checked = 'checked = "checked"';
    }elseif(!empty($data['default']) && $data['default'] == $item_key) {
      $checked = 'checked = "checked"';
    }
?>
<label class="radio-inline">
<input type="radio"<?=$attr?> name="<?=$data['key']?>" value="<?=$item_key?>" <?=$checked?>> <?=$item_value?>
</label>

<?php
  endforeach;
endif;

  $this->load->view("edit_form/parts/form_base_btm", array('data' => $data));
//--------------------------------------------------------
endif;
?>
