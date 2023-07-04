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
  $value = '';
  if(!empty($data['value'])) {
    $value = fn_esc($data['value']);
  }
?>

<textarea name="<?=$data['key']?>"<?=$attr?>><?=$value?></textarea>

<?php
  $this->load->view("edit_form/parts/form_base_btm", array('data' => $data));
//--------------------------------------------------------
endif;
?>
