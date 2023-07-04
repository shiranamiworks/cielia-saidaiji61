<?php
if(!empty($data)) :
//--------------------------------------------------------

  $attr = '';
  if(!empty($data['attr'])) {
    foreach($data['attr'] as $key => $attr_val) {
      $attr .= ' ' . $key . '="' . $attr_val . '"';
    }
  }
  $value = '';
  if(!empty($data['value'])) {
    $value = 'value="'.fn_esc($data['value']).'"';
  }
  
?>
<dl>
<dt><?=$data['label']?></dt>
<dd>
<input type="text" name="<?=$data['key']?>"<?=$attr?> <?=$value?>>
</dd>
</dl>

<?php
endif;
?>
