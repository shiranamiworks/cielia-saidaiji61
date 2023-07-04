<?php
if(!empty($data)) :
//--------------------------------------------------------
  $class_box = 'field-box';
  $class_itemname = 'itemname';
  $class_itembody = 'field-item';

  //form group項目内のアイテムの場合
  if(!empty($data['group_block'])) {
    $class_box = 'block-body';
    $class_itemname = 'block-itemname';
    $class_itembody = 'block-item';
  }
  
  if(!empty($data['error'])) {
    $class_box .= ' error';
  }

  if(!empty($data['type']) && $data['type'] == 'hidden') {
    $class_box .= ' no-disp';
  }

?>
<div class="<?=$class_box?>">
<label class="<?=$class_itemname?>"><?=str_replace('[br]','<br>' , $data['label'])?>:
<?php
if(!empty($data['require'])) :
?>
<?php /*<span class="require">＊</span> */ ?>
<span style="font-size:70%" class="label label-danger">必須</span>
<?php
endif;

if(!empty($data['label_comment'])) :
?>
<span class="comment"><?=$data['label_comment']?></span>
<?php
endif;
?>
</label>
<div class="<?=$class_itembody?>">
<?php if(!empty($data['comment_top'])) : ?>
<p class="comment comment-top"><?=$data['comment_top'] ?></p>
<?php endif; ?>

<?php
//--------------------------------------------------------
endif;
?>
