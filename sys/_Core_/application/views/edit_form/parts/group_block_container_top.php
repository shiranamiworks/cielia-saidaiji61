<?php
if(empty($blockname)) {
  $blockname= 'Block';
}
if(empty($block_number)) {
  $block_number = 1;
}
if(empty($blocklabel)) {
  $blocklabel= 'ブロック';
}
?>

<div class="block-container block-<?=$blockname?>" data-block-num="<?=$block_number?>">
<p class="block-name"><span class="block_number_text"><?=$blocklabel?><?=$block_number?></span> 
<?php if(!empty($btn_delete)) : ?>
<button type="button" class="btn-flat white small btn-block-delete" style="margin-left:5px"><i class="icon-remove"></i>削除</button>
<?php endif; ?>
</p>

<input type="hidden" name="_meta_[<?=$blockname?>][block_number][]" value="<?=$block_number?>">
<div class="block-lists">
