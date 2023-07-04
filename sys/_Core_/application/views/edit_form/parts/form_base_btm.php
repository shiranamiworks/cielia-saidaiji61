<?php
if(!empty($data)) :
//--------------------------------------------------------
?>

<?php if(!empty($data['comment_btm'])) : ?>
<p class="comment comment-btm"><?=$data['comment_btm'] ?></p>
<?php endif; ?>


<?php
if(!empty($data['error'])) {
  echo str_replace('[br]','' , $data['error']);
}
?>

</div>
</div>
<?php
//--------------------------------------------------------
endif;
?>
