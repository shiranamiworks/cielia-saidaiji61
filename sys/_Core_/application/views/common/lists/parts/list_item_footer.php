<?php
if(!empty($data)) :
//--------------------------------------------------------

?>

<div class="col-md-12">
<?php
if(empty($data['result'])) :
//------------------------------------------------------------------
// データなし
?>
    <p><?=DATA_NAME?>の登録がありません</p>
    
<?php
//------------------------------------------------------------------
else:
  if(isset($data['listSetting']['delete_check']) && $data['listSetting']['delete_check'] === true) : 
?>

<button type="button" id="btnSelectDelete" class="btn btn-danger btn-sm" disabled="disabled"><i class="icon-trash btn-selected-item-del"></i>選択した<?=DATA_NAME?>を削除</button>

<?php
  endif;
endif;
?>

</div>

<?php
//--------------------------------------------------------
endif;
?>

