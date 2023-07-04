<?php
if(!empty($data)) :
//--------------------------------------------------------
  $this->load->view("edit_form/parts/form_base_top", array('data' => $data));

?>

<div class="form-group-blocks group-<?=$data['key']?>">


<?= $data['group_blocks_html'] ?>


<!-- .form-group-blocks --></div>



<!--アイテム登録用テンプレート-->
<script id="block_<?=$data['key']?>_tmpl" type="text/x-jquery-tmpl">
<?= $data['group_blocks_tmpl_html'] ?>
</script>


<button type="button" id="btnAdd-<?=$data['key']?>" class="btn-flat default"><i class="icon-plus"></i><?=$data['add_button']?></button>

<script>
$(function(){
    $("#btnAdd-<?=$data['key']?>").on('click',function(){
        var max_add_num = <?=$data['max_block_num']?>;
        var max_block_num = 0;
        var block_num = $('.block-container.block-<?=$data['key']?>').length;
        if(block_num < max_add_num) {
            $('.block-container.block-<?=$data['key']?>').each(function(){
                var this_num = $(this).data('block-num');
                if(max_block_num < this_num) {
                    max_block_num = this_num;
                }
            });

            $('#block_<?=$data['key']?>_tmpl').tmpl({ block_num: (max_block_num + 1) }).appendTo('.form-group-blocks.group-<?=$data['key']?>');
            
            //削除ボタンイベント
            setEventBlockDelete_<?=$data['key']?>();
            /*
            $('.group-<?=$data['key']?> .btn-block-delete').off('click').on('click', function(){
              if(window.confirm('このブロックを削除してもよろしいですか？')) {
                $(this).parents('.block-container').fadeOut(500 , "linear" , function(){
                  $(this).remove();
                  dispChkBtnAdd_<?=$data['key']?>();

                  //ブロック名（ナンバー）表示修正
                  $('.block-container.block-<?=$data['key']?> .block-name').each(function(i , val){
                    $(this).find('.block_number_text').html('ブロック'+(i+1));
                  });
                });
              }
            });
            */

            dispChkBtnAdd_<?=$data['key']?>();
            setEventopenImgSelectWindow();
            setEditor('editor_grp_<?=$data['key']?>_'+(max_block_num + 1));
        }
    });
  
    //追加ボタンの表示・非表示切り替え
    var dispChkBtnAdd_<?=$data['key']?> = function() {
        var max_add_num = <?=$data['max_block_num']?>;
        var block_num = $('.block-container.block-<?=$data['key']?>').length;
        if(block_num >= max_add_num) {
            $("#btnAdd-<?=$data['key']?>").hide();
        } else {
            $("#btnAdd-<?=$data['key']?>").show();
        }
    }

    //ブロック削除イベントセット
    var setEventBlockDelete_<?=$data['key']?> = function() {
      $('.group-<?=$data['key']?> .btn-block-delete').off('click').on('click', function(){
        if(window.confirm('このブロックを削除してもよろしいですか？')) {
          $(this).parents('.block-container').fadeOut(500 , "linear" , function(){
            $(this).remove();
            dispChkBtnAdd_<?=$data['key']?>();

            //ブロック名（ナンバー）表示修正
            $('.block-container.block-<?=$data['key']?> .block-name').each(function(i , val){
              $(this).find('.block_number_text').html('ブロック'+(i+1));
            });
          });
        }
      });
    }

    setEventBlockDelete_<?=$data['key']?>();
});

</script>


<?php
  $this->load->view("edit_form/parts/form_base_btm", array('data' => $data));
//--------------------------------------------------------
endif;
?>
