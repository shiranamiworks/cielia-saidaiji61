<?php
if(!empty($data)) :
//--------------------------------------------------------

  $this->load->view("edit_form/parts/form_base_top", array('data' => $data));
  
  $value = '';
  if(!empty($data['value'])) {
    $value = $data['value'];
  }
  $base_key = str_replace(array('[' , ']') , '' , $data['key']);
?>


<div class="row" id="imgCheckList_<?= $data['design_pos'] ?>">
<div class="list_container" data-design-pos="<?= $data['design_pos'] ?>"></div>
<?php
/*
if(!empty($data['data'])) :
  foreach($data['data'] as $item_key => $item_value) :
    
?>
<div class="col-sm-6 col-md-4" style="margin-top:15px">
  <div class="img-layout-list">
  <dl>
    <dt><img src="<?= $item_value['preview_image'] ?>"></dt>
    <dd class="lead-text text-center"><?= $item_value['name'] ?>
    <?php
    if(!empty($data['value']) && $item_key == $data['value']) :
    ?>
    <span class="label label-primary"><small>現在このデザインが登録されています</small></span>
    <?php
    endif;
    ?>
    </dd>
    <dd class="btn-select" data-design-id="<?= $item_key ?>"><a href="javascript:void(0);" class="btn btn-success btn-sm">このデザインを選択</a></dd>
  </dl>
  </div>
</div>

<?php
  endforeach;
endif;
*/
?>
<script id="tmpl_imgCheckList_<?= $data['design_pos'] ?>" type="text/x-jquery-tmpl">
<div class="col-sm-6 col-md-6 col-lg-6 chklist-item{{if id == '<?= $value ?>'}} item-registered{{/if}}" style="margin-top:15px">
  {{if id == '<?= $value ?>'}} 
  <div class="primary-item"><span class="label label-danger">現在このデザインが登録されています</span></div>
  {{else}} 
  <div class="notprimary-item" style="display:none"><span class="label label-default" style="background-color:#333">このデザインが選択されました</span></div>
  {{/if}} 
  <div class="img-layout-list">
  <dl>
    <dt><img src="${preview_image}" class="designimg_preview" alt="${name}"></dt>
    <dd class="lead-text text-center">${name} 
    </dd>
    <dd class="btn-select" data-design-id="${id}"><a href="javascript:void(0);" class="btn btn-success btn-s">このデザインを選択</a></dd>
  </dl>
  </div>
</div>
</script>
<input type="hidden" class="selected_design_val" name="<?= $data['key'] ?>" value="<?= $value ?>">

</div>

<?php 
//groupのテンプレート生成の場合はscriptを含めない
if(empty($data['group_tmpl'])) : 
?>
<script>

var buildImgChkList_<?= $data['design_pos'] ?> = function(data){
  $("#imgCheckList_<?= $data['design_pos'] ?> .list_container").empty();
  $('#tmpl_imgCheckList_<?= $data['design_pos'] ?>').tmpl(data).appendTo("#imgCheckList_<?= $data['design_pos'] ?> .list_container");

  var selected_design = $("#imgCheckList_<?= $data['design_pos'] ?>").find(".selected_design_val").val();
  var selected = 0;
  $("#imgCheckList_<?= $data['design_pos'] ?> .img-layout-list").each(function(i , elem){
    var t_design_id = $(this).find('.btn-select').data("design-id");
    if(t_design_id == selected_design) {
      selected = i;
    }
  });
  if(selected == 0) {
    var set_design_id = $("#imgCheckList_<?= $data['design_pos'] ?> .img-layout-list").first().find('.btn-select').data("design-id");
    $("#imgCheckList_<?= $data['design_pos'] ?>").find(".selected_design_val").val(set_design_id);
  }

  $("#imgCheckList_<?= $data['design_pos'] ?> .img-layout-list").imgCheckbox({
    'checkMarkPosition' : 'top-left',
    'checkMarkSize' : '60px',
    'preselect' : [selected],
    'radio' : true,
    'addToForm' : false,
    'graySelected' : false,
    'onload' : function() {
      //$('#imgCheckList_<?= $data['design_pos'] ?> .imgChkWrapper').tile();
      setDesignImgZoom();
      setDesignSelected();
      //$('.imgChkWrapper').matchHeight();
    }/*,
    'onclick' : function(obj) {
      var select_design_id = $(obj).data("design-id");
      var select_design_pos = $(obj).parents('.list_container').data("design-pos");
      if(select_design_pos == 'base') {
        var val = $("#imgCheckList_"+select_design_pos).find(".selected_design_val").val();
        if(val != select_design_id) {
          //ベースの場合、クリック選択したデザインIDが現在の選択から変わっていれば
          //他のポジションの選択肢をクリアする（タブ切り替えの際に以前の選択肢が一瞬表示されてしまうため）
          $(".list_container").not(':first').empty();
        }
      }
      $("#imgCheckList_"+select_design_pos).find(".selected_design_val").val(select_design_id);

    }
    */
    ,

    'onclick' : function(obj) {
      var select_design_id = $(obj).data("design-id");
      var select_design_pos = $(obj).parents('.list_container').data("design-pos");
      var message = '<br>デザインの変更は「登録」ボタンがクリックされるまでサイトへは反映されません。';
      if(select_design_pos == 'base') {
        message += '<br>ベースデザインを変更された場合、ヘッダーや各種コンテンツのデザインタイプを選択し直す必要があります';
      }
      /*
      notif({
        msg: message,
        width : "all",
        type: "success",
        position: "center",
        timeout:20000
      });
      */
      $.toast({
          heading: message,
          //text: message2,
          showHideTransition: 'slide',
          bgColor: '#333',
          icon: 'info',
          hideAfter : 10000,
          position : 'top-right',
          stack: false
      })

      $('.notprimary-item').hide();
      $(obj).parents('.chklist-item').find('.notprimary-item').show();
    }
  });
};

var imgCheckListData_<?= $data['design_pos'] ?> = [];
<?php
//base designの場合、ここにデザインパターンのデータを入れて、buildImgChkListを呼び出す際に利用する
//（実際に呼び出すのはsetting/edit/js/orig.php(js)から）
if(!empty($data['data'])) :
  foreach($data['data'] as $item_key => $item_value) :
?>
imgCheckListData_<?= $data['design_pos'] ?>.push({
  'id' : '<?= $item_key ?>',
  'name' : '<?= $item_value['name'] ?>',
  'preview_image' : '<?= $item_value['preview_image'] ?>'
});
<?php
  endforeach;
endif;
?>

</script>
<?php 
endif; 
?>

<?php
  $this->load->view("edit_form/parts/form_base_btm", array('data' => $data));
//--------------------------------------------------------
endif;
?>
