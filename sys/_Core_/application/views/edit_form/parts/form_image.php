<?php
if(!empty($data)) :
//--------------------------------------------------------

  $this->load->view("edit_form/parts/form_base_top", array('data' => $data));

  $value = '';
  if(!empty($data['value'])) {
    $value = 'value="'.fn_esc($data['value']).'"';
  }
?>

<div class="btn-flat gray btn-image-select" data-image-id="<?=$data['image_id']?>"><i class="icon-picture"></i>イメージを選択</div>
<input type="hidden" name="<?=$data['key']?>" class="img_hidden_value" id="img_hidden_<?=$data['image_id']?>" <?=$value?>>
<div id="img_prev_<?=$data['image_id']?>" class="img_preview"></div>
<p><button type="button" id="img_delete_<?=$data['image_id']?>" class="btn-flat white btn-image-delete" style="display:none">選択を解除</button></p>


<?php
  $this->load->view("edit_form/parts/form_base_btm", array('data' => $data));
//--------------------------------------------------------
endif;
?>
