<?php
if(!empty($data)) :
//--------------------------------------------------------

  $attr = '';
  if(!empty($data['attr'])) {
    foreach($data['attr'] as $key => $attr_val) {
      if(!empty($attr)) $attr .= ' ';
      $attr .= $key . '="' . $attr_val . '"';
    }
  }
  
?>
<dl>
<dt><?=$data['label']?></dt>
<dd>
<div class="ui-select">
<?php
    echo form_dropdown($data['key'] , $data['data'] , (!empty($data['value']) ? $data['value'] : '') , $attr);
?>
</div>
</dd>
</dl>

<?php
endif;
?>
