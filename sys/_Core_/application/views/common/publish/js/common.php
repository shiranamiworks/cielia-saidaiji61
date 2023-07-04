<script>
$(function(){
  //公開キャンセルボタン
  $("#btnPublishCancel").on('click' , function(){
    window.parent.publishModalClose(false);
  });
  //公開切替後の閉じるボタン（リロードさせる）
  $("#btnPublishCancelAndReload").on('click' , function(){
    window.parent.publishModalClose(true);
  });
});
</script>
