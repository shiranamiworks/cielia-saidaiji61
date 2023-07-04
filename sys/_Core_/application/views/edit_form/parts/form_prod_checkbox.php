<?php
if(!empty($data)) :
//--------------------------------------------------------
  
  $this->load->view("edit_form/parts/form_base_top", array('data' => $data));
  
  $key = $data['key'].'[]';

  $attr = '';
  if(!empty($data['attr'])) {
    foreach($data['attr'] as $key => $attr_val) {
      $attr .= ' ' . $key . '="' . $attr_val . '"';
    }
  }
?>

<input type="hidden" name="<?=$data['key']?>" value="">
<?php
if(!empty($data['prod_category'])) :
  //大カテゴリ
  foreach($data['prod_category'] as $cat_key => $parent_category) :
    $parent_categoryname = $parent_category['label'];
?>
<div style="margin:10px 0 0"><strong><?= $parent_categoryname ?></strong></div>
<?php
    foreach($parent_category['sub_category'] as $subcat_key => $sub_category) :
      $checked = '';
      if(!empty($data['value']) && in_array($subcat_key , $data['value'])) {
        $checked = 'checked = "checked"';
      }
?>
<label class="checkbox-inline">
<input type="checkbox"<?=$attr?> name="<?=$key?>" value="<?=$subcat_key?>" <?=$checked?>> <?=$sub_category['label']?>
</label>

<?php
    endforeach;
  endforeach;
endif;

  $this->load->view("edit_form/parts/form_base_btm",  array('data' => $data));
//--------------------------------------------------------
endif;
?>
