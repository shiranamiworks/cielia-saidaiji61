<script>
var toolbar_setting = [
    { name: 'document', groups: [ 'mode'] },
    { name: 'clipboard', groups: [ 'undo' ] },
    { name: 'links', groups: [ 'links' ] },
    { name: 'insert', groups: [ 'insert' ] },
    { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
    { name: 'paragraph', groups: [ 'list', 'blocks', 'align' ] },
    '/',
    { name: 'styles', groups: [ 'styles','Font','FontSize' ] },
    { name: 'colors', groups: [ 'TextColor','BGColor' ] }
];

var CK_extraplugins = 'youtube';
var add_css_class =  [
    { name : 'Image on Left',element : 'img', attributes :{ 'style' : 'padding: 5px; margin-right: 5px', 'align' : 'left'} },
    { name : 'Image on Right',element : 'img', attributes :{ 'style' : 'padding: 5px; margin-left: 5px', 'align' : 'right'} }
];
var content_css_filepath = [
    '<?=base_url()?>js/ckeditor-full/contents.css'
]
$(function(){
});

//Ckeditor の起動
function setEditor(target)
{
    //スタイル追加
    if(add_css_class.length > 0) {
        //CKEDITOR.stylesSet.add( 'default' , add_css_class );
    }

    //create CKEditor
    var editor = CKEDITOR.replace( target,
    {
      language : 'ja',
      enterMode : CKEDITOR.ENTER_BR,
      //contentsCss : content_css_filepath,
      width : 800,
      resize_maxWidth : 800,
      height : 500,
      extraPlugins : CK_extraplugins,
      toolbarGroups : toolbar_setting,
      removeButtons : 'Cut,Copy,Paste,PasteText,PasteFromWord,Underline,Subscript,Superscript,Save,NewPage,DocProps,Preview,Print,Flash,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,BidiLtr,BidiRtl,NumberedList,BulletedList,Blockquote,Styles,Format,Table,Anchor,CreateDiv',
      filebrowserImageBrowseUrl : '<?=site_url($dirname."/media/lists")?>?type=image',
      filebrowserBrowseUrl: '<?=site_url($dirname."/media/lists")?>?type=file',
      filebrowserWindowWidth : 1020,
      filebrowserWindowHeight : 700,
      image_previewText : 'プレビューダミーテキストプレビューダミーテキストプレビューダミーテキストプレビューダミーテキストプレビューダミーテキストプレビューダミーテキスト<br>プレビューダミーテキストプレビューダミーテキスト<br>プレビューダミーテキストプレビューダミーテキストプレビューダミーテキスト',
      removeDialogTabs : 'flash:advanced;image:Link'
    });
}

//CKEditor側のダイアログへ選択された画像パスまたはファイルパスを渡す
function CKEditor_dialog_setValue(path)
{
    var dialog = CKEDITOR.dialog.getCurrent();
    if(dialog.getName() == 'image') {
      dialog.setValueOf('info','txtUrl',path);
    } else if(dialog.getName() == 'link') {
      dialog.setValueOf('info','url',path);
      dialog.setValueOf('info','protocol','');
    }
    return false;
}


//画像選択ボタンからの画像選択ウィンドウ立ち上げ
function setEventopenImgSelectWindow()
{
  $('.btn-image-select').off('click').on('click' , function(){
    var id = $(this).data('image-id');
    var url = '<?=site_url($dirname."/media/lists")?>?type=image&called=btnSelect&target='+id;
    window.open(url , 'win1' , 'width=1020,height=700,toolbar=no,location=no,menubar=no,scrollbars=yes');
    return false;
  });
}

setEventopenImgSelectWindow();


var remodal_options = { 'hashTracking' : false };
var remodalInst_img= $('[data-remodal-id=imgModal]').remodal(remodal_options);

//画像プレビューの拡大モーダルイベントセット
function setEventClickImgPreview() {

  //画像プレビュー
  $(".img_preview > img").off('click').on('click' , function(){
    var t_image = $(this).parent().prevAll('.img_hidden_value').attr('value');
    if(t_image) {
        $("#imgModal").find('.modal-body > p').empty().append($('<img/>').attr("src" , t_image).css('maxWidth','600px'));
        $("#imgModal").find('.modal-title').empty().text('画像プレビュー');
        remodalInst_img.open();
        
        //close event set
        $("#imgModal").find('.remodal-close').off('click').on('click' , function(){
            remodalInst_img.close();
        });
     }
    return false;
  });
}

//画像選択ボタンから選択された画像をクリアする
function setEventClickImgClear() {
  $('.btn-image-delete').off('click').on('click' , function(){
    var id = $(this).parent().prevAll('.btn-image-select').data('image-id');
    var obj_hidden = $("#img_hidden_"+id);
    var obj_image = $("#img_prev_"+id);
    if($(obj_hidden).val())
    {
        $(obj_hidden).val("");
        $(obj_image).empty();
    }
    $(this).hide();
    return false;
  });
}

//画像選択ボタンの選択画像がすでに選択されていた場合、
//画面ロード時にプレビューを初期表示させておく処理
function setPreviewImg() {
  $('.img_hidden_value').each(function(i , val){
    var image_path = $(this).val();
    if(image_path) {
      var target_image_id = $(this).prevAll('.btn-image-select').data('image-id');
      if(target_image_id) {
        $('#img_prev_'+target_image_id).empty().append('<img src="'+image_path+'" />');
        $('#img_delete_'+target_image_id).show();
      }
    }
  });

  setEventClickImgClear();
  setEventClickImgPreview();

}
setPreviewImg();


//送信、プレビュー、キャンセル、各ボタンのクリックイベント
function setClickFormBtn() {

  var submit_url = '<?= $submit_url ?>';
  $('#btnSubmit').on('click' , function(){
    $('#editForm').attr({'action' : submit_url , 'target' : '_self' });
    $('#editForm').submit();
  });

  var cancel_url = '<?= $cancel_url ?>';
  $('#btnCancel').on('click' , function(){
    window.location.href = cancel_url;
  });

  var preview_url = '<?= $preview_url ?>?url=';
  var preview_site_url = '<?= $preview_site_url ?>';
  $('#btnPreview').on('click' , function(){

    win_w = Math.floor($(window).width()*0.9);
    win_h = Math.floor($(window).height()*0.9);
    var preview_win = window.open("about:blank", 'preview_win', 'width='+win_w+', height='+win_h+', scrollbars=yes , menubar=no');
    $('#editForm').attr({'action' : preview_url + ($('#previewSiteUrl').val() ? $('#previewSiteUrl').val() : preview_site_url) , 'target' : 'preview_win'});

    $('#editForm').submit();
  });

}

setClickFormBtn();


//タブ上下の動作を連動
$('.nav-tabs-top a[data-toggle="tab"]').on('click', function(){
    $('.nav-tabs-bottom li.active').removeClass('active')
    $('.nav-tabs-bottom a[href="'+$(this).attr('href')+'"]').parent().addClass('active');
})

$('.nav-tabs-bottom a[data-toggle="tab"]').on('click', function(){
    $('.nav-tabs-top li.active').removeClass('active')
    $('.nav-tabs-top a[href="'+$(this).attr('href')+'"]').parent().addClass('active');
})


</script>
