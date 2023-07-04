<script>
var remodal_options = { 'hashTracking' : false };
var remodalInst_upload = null;
var remodalInst_del = null;
var remodalInst_img = null;
var remodalInst_pdf = null;

var blank_page_url = '<?php echo $blank_page_url; ?>';
var upload_file_maxsize = parseInt('<?php echo $upload_maxsize; ?>');

$(function(){

    //ToolTip
    $('[data-toggle="tooltip"] , .tooltip-item').tooltip();

    //modal instance
    remodalInst_upload = $('[data-remodal-id=uploadModal]').remodal(remodal_options);
    remodalInst_del = $('[data-remodal-id=delModal]').remodal(remodal_options);
    remodalInst_img= $('[data-remodal-id=imgModal]').remodal(remodal_options);
    remodalInst_pdf= $('[data-remodal-id=pdfModal]').remodal(remodal_options);

    //upload
    $(".btn-upload").click(function(){
        
        remodalInst_upload.open();

        //close event set
        $("#uploadModal").find('.remodal-close').off('click');
        $("#uploadModal").find('.remodal-close').on('click' , function(){
            remodalInst_upload.close();
        });

        $("#uploadModal").find("#btnUpload").off('click');
        $("#uploadModal").find("#btnUpload").on('click' , function(){
            $("#uploadModal").find("#uploadForm").submit();
        });

        return false;
    });




    // item hover 
    $(".media-list li").each(function(){
        $(".over-menu" , this).data("active" , "0");
    }).hover(
        function(){
            $(this).find(".over-menu").show();
            $(this).find(".menu-item").off('mouseenter');
            $(this).find(".menu-item").on('mouseenter' , function(){
                $(this).find("img").css("opacity",0.7);
            });
            $(this).find(".menu-item").off('mouseleave');
            $(this).find(".menu-item").on('mouseleave' , function(){
                $(this).find("img").css("opacity",1);
            });
        },
        function(){
            if($(".over-menu" , this).data("active") == "0") {
                $(this).find(".over-menu").hide();
            }
        }
    );


    //削除処理
    $('.action-delete').click(function(){
        var t_file = $(this).parent(".over-menu");
        var delete_file_id = t_file.find(".filename").data('id');
        var delete_filename = t_file.find(".filename").text();
        if(delete_file_id && delete_filename) {
            //モーダルが表示されるため、active値を1にして、画像にオーバーレイ表示しているメニューが消えないようにする
            t_file.data("active" , "1");

            $("#delModal").find('#deleteID').val(delete_file_id);
            remodalInst_del.open();

            //close event set
            $("#delModal").find('.remodal-close').off('click');
            $("#delModal").find('.remodal-close').on('click' , function(){
                remodalInst_del.close();
            });
            
            $("#delModal").find("#btnDelete").off('click');
            $("#delModal").find("#btnDelete").on('click' , function(){
                if($("#delModal").find("#deleteID").val()) {
                    $("#delModal").find("#delForm").submit();
                }
            });
        }
        return false;
    });

    //画像プレビュー
    $(".file-image > a").on('click' , function(){
        var t_image = $(this).data('imgpath');
        var image_expstr = $(this).attr('title');
        if($(this).data('footer')) {
            image_expstr += '<br>（'+escapedHtml($(this).data('footer'))+'）';
        }
        if(t_image) {
            $("#imgModal").find('.modal-body > p').empty().append($('<img/>').attr("src" , t_image));
            $("#imgModal").find('.modal-footer p.filename-str').html(image_expstr);
            remodalInst_img.open();
            
            //close event set
            $("#imgModal").find('.remodal-close').off('click');
            $("#imgModal").find('.remodal-close').on('click' , function(){
                remodalInst_img.close();
            });
         }
        return false;
    });

    //PDFプレビュー
    $(".file-pdf > a").on('click' , function(){
        var t_pdf = $(this).attr('href');
        if(t_pdf) {
            $("#pdfModal").find('iframe').attr("src",t_pdf);
            $("#pdfModal").find('.modal-footer #pdfFilePath').val(t_pdf);
            remodalInst_pdf.open();
            

            //close event set
            $("#pdfModal").find('.remodal-close').off('click');
            $("#pdfModal").find('.remodal-close').on('click' , function(){
                $('#pdfhModal').find('iframe').attr("src",blank_page_url);
                remodalInst_pdf.close();
            });

         }
        return false;
    });
    $(document).on('closed', '#pdfModal', function (e) {
        $('#pdfModal').find('iframe').attr("src",blank_page_url);
    });

    $(document).on('closed', '#delModal', function (e) {
        $(".media-list li").each(function(){
            $(".over-menu" , this).data("active" , "0").hide();
        });
        $("#deleteFilename").val("");
    });

    setUploadfileSelectEvent();
});

var deleteRun = function() {
    if($("#deleteFilename").val()) {
        $("#delForm").submit();
    }
}
//アップロードファイルのプレビューセットイベント
var setUploadfileSelectEvent = function() {
    var obj = '.fileup-items';
    var selfFile = $(obj),
      selfInput = $(obj).find('input[type=file]');
    if (window.FileReader) {
        selfInput.off('change');
        selfInput.on('change' , function(){
            var file = $(this).prop('files')[0],
            fileRdr = new FileReader(),
            previewArea = $(this).parents('.fileup-items').find('.preview');
            
            if(!this.files.length){
                if (previewArea.css('display') == 'block') {
                    previewArea.hide();
                    return;
                }
            } else {
                if(file.type.match('image.*') || file.type.match('pdf.*') || file.type.match('excel.*') || file.type.match('spreadsheet.*') || file.type.match('word.*') ){
                    $(this).parent('div').find('.alert-text').remove();
                    if(file.size > upload_file_maxsize*1024) {
                        var alert_text = 'ファイル容量が大きすぎます。'+upload_file_maxsize+'KB以下のファイルを選択してください。';
                        $(this).parent('div').find('.alert-text').remove();
                        $(this).parent('div').append($('<p/>').addClass('alert-text').text(alert_text));
                        return false;
                    }

                    if (previewArea.css('display') != 'block') {
                        previewArea.show();
                    }
                    fileRdr.onload = function() {
                        var preview_src = BASE_URL+'/img/icons/ico_question.png';
                        if(file.type.match('image.*')) {
                            preview_src = fileRdr.result;
                        }else if(file.type.match('pdf.*')) {
                            preview_src = BASE_URL+'/img/icons/ico_pdf2.png';
                        }else if(file.type.match('excel.*') || file.type.match('spreadsheet.*')) {
                            preview_src = BASE_URL+'/img/icons/ico_excel.png';
                        }else if(file.type.match('word.*')) {
                            preview_src = BASE_URL+'/img/icons/ico_word.png';
                        }
                        previewArea.find('img').attr('src', preview_src);
                    }
                    fileRdr.readAsDataURL(file);
                    
                } else {
                    if (previewArea.css('display') == 'block') {
                        previewArea.hide();
                        return;
                    }
                }
            }
        });
    }
}

</script>