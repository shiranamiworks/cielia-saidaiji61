<script>
var remodal_options = { 'hashTracking' : false };
var remodalInst_file = null;

var blank_page_url = '<?php echo $blank_page_url; ?>';

var prevModal_max_w = 500;
var prevModal_max_h = 500;

var clickBtnSelectFlag = <?php echo $btnSelect; ?>;
var btnSelectTargetId = '<?php echo $target; ?>';
$(function(){

    //ToolTip
    $('[data-toggle="tooltip"] , .tooltip-item').tooltip();

    //modal instance
    remodalInst_file = $('[data-remodal-id=fileModal]').remodal(remodal_options);

    $(".media-select-list li").on('click' , function(){
        var t_filename = $(this).find('.fileinfo').data('filename');
        var t_filepath = $(this).find('.fileinfo').data('filepath');
        var t_filesmpsizepath = $(this).find('.fileinfo').data('filesmpsizepath');
        var t_filethumbpath = $(this).find('.fileinfo').data('filethumbpath');
        var t_filetype = $(this).find('.fileinfo').data('filetype');
        if(t_filepath) {
          var img_src = '';
          if(t_filetype == 'image') {
            img_src = t_filesmpsizepath;
          } else if(t_filetype == 'pdf') {
            img_src = BASE_URL+'/img/icons/ico_pdf2.png';
          } else if(t_filetype == 'word') {
            img_src = BASE_URL+'/img/icons/ico_word.png';
          } else if(t_filetype == 'excel') {
            img_src = BASE_URL+'/img/icons/ico_excel.png';
          }
          var preview_area = $("#fileModal").find('.modal-body > div');
          preview_area.empty().append($('<img/>').attr({"src" : img_src , "id" : "prevImg"}));
          preview_area.append($('<p/>').css({'padding-top':'10px' , 'font-size':'0.8em'}).text(t_filename));
          remodalInst_file.open();
            
          var img_w = $("#prevImg").width();
          var img_h = $("#prevImg").height();
          
          if(img_w > img_h) {
            if(img_w > prevModal_max_w) {
              $("#prevImg").css("width",prevModal_max_w);
            }
          } else {
            if(img_h > prevModal_max_h) {
              $("#prevImg").css("height",prevModal_max_h);
            }
          }
          //close event set
          $("#fileModal").find('.remodal-close').off('click');
          $("#fileModal").find('.remodal-close').on('click' , function(){
              remodalInst_file.close();
          });

          //選択ボタン
          $("#fileModal").find('#btnSelect').off('click');
          $("#fileModal").find('#btnSelect').on('click' , function(){
            if(!clickBtnSelectFlag) {
              setValueEditorDialog(t_filepath);
            } else {
              setValueBtnSelectImg(t_filepath , t_filethumbpath , btnSelectTargetId);
            }
          });

        }
        return false;
    });
  

  //閉じるボタン
  $('.btn-win-close').on('click' , function(){
    window.close();
  });

});


//選択された画像ファイル名をWYSIWYGエディタ側へ渡す
function setValueEditorDialog(path){
  if( window.opener && !window.opener.closed ){
    window.opener.CKEditor_dialog_setValue(path);
  }
  window.close();
  return false;
}

//画像選択ボタンから起動した場合
//選択されたファイルを選択ボタン側へ渡す
function setValueBtnSelectImg(path , thumb_path , target_id) {
  
  if( window.opener && !window.opener.closed ){
    var obj_hidden = $("#img_hidden_"+target_id,window.opener.document);
    var obj_image = $("#img_prev_"+target_id,window.opener.document);
    var obj_btn_del = $("#img_delete_"+target_id,window.opener.document);
    $(obj_hidden).val(path);
    setPreviewImg(obj_image , thumb_path);
    $(obj_btn_del).show();

    window.opener.setEventClickImgClear();
    window.opener.setEventClickImgPreview();
  }
  window.close();
  return false;
}

function setPreviewImg(obj_image , path)
{
    $(obj_image).empty().append('<img src="'+path+'">');
}



</script>
