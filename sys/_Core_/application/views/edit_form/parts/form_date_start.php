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
    if((int)$data['value'] == 0) {
      $value = '';
    } else {
      $value = 'value="'.date('Y/m/d H:i', strtotime($data['value'])).'"';
    }
  }
?>

<input name="<?=$data['key']?>" id="<?=$data['key']?>"<?=$attr?> type="text" <?=$value?>>

<script>
$(function(){
  var term_start_<?=$data['key']?> = $("#<?=$data['key']?>");

  //timepicker
  term_start_<?=$data['key']?>.datetimepicker({
      format : 'Y/m/d H:i',
      defaultTime : '00:00',
      lang : 'ja'
  });
});
</script>

<?php
  $this->load->view("edit_form/parts/form_base_btm", array('data' => $data));
//--------------------------------------------------------
endif;
?>
