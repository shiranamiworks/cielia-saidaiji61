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
    $value = $data['value'];
  }

  $editor_id = $data['key'];
  if(!empty($data['editor_id'])) {
    $editor_id = $data['editor_id'];
  }
?>

<textarea name="<?=$data['key']?>"<?=$attr?> id="<?=$editor_id?>"><?=$value?></textarea>

<?php 
//groupのテンプレート生成の場合はscriptを含めない
if(empty($data['group_tmpl'])) : 
?>
<script>
$(function(){
  setEditor("<?=$editor_id?>");
});
</script>
<?php 
endif; 
?>

<?php
  $this->load->view("edit_form/parts/form_base_btm", array('data' => $data));
//--------------------------------------------------------
endif;
?>
