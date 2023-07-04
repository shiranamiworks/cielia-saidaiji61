<script>

var remodal_options = { 'hashTracking' : false };
var remodalInst_del = null;
var remodalInst_pub = null;
var remodalInst_chkdel = null;

var search_base_url = '<?php echo $search_base_url; ?>';
var blank_page_url = '<?php echo $blank_page_url; ?>';

$(function(){

    //ToolTip
    $('.tooltip-item').tooltip();

    remodalInst_del = $('[data-remodal-id=delModal]').remodal(remodal_options);
    remodalInst_pub = $('[data-remodal-id=publishModal]').remodal(remodal_options);
    remodalInst_chkdel = $('[data-remodal-id=chkselectDeleteModal]').remodal(remodal_options);

    //delete checkbutton
    $("input[type=checkbox].delete_check , input[type=checkbox].master").on('change' , function(){
        var delete_check_arr = [];
        $("input.delete_check").each(function(){
            if($(this).is(':checked')) {
                delete_check_arr.push($(this).val());
            }
        });
        if(delete_check_arr.length > 0) {
            $("#btnSelectDelete").prop('disabled' , false);
        } else {
            $("#btnSelectDelete").prop('disabled' , true);
        }
    });

    //select delete button
    $("#btnSelectDelete").on('click' , function(){
        remodalInst_chkdel.open();

        //close event set
        $("#chkselectDeleteModal").find('.remodal-close').off('click');
        $("#chkselectDeleteModal").find('.remodal-close').on('click' , function(){
            remodalInst_chkdel.close();
        });

        $("#chkselectDeleteModal").find("#btnChkSelectDelete").off('click');
        $("#chkselectDeleteModal").find("#btnChkSelectDelete").on('click' , function(){
            $("#delSelectForm").submit();
        });
    });

    //delete 
    $(".btn-delete").on('click' , function(){
      var t_data_id = $(this).data('id');
      var t_data_name = $(this).data('name');
      if(t_data_id) {
        $("#delModal").find('#delDataName').text(t_data_name);
        $("#delModal").find('#deleteID').val(t_data_id);
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

    //公開切り替え
    $('.link_publish').on('click' , function(){
        var tid = $(this).data("id");
        var load_url = $(this).data("link");
        if(tid) {
            $('#publishModal').find('iframe').attr("src",load_url);
            remodalInst_pub.open();

            //close event set
            $("#publishModal").find('.remodal-close').off('click');
            $("#publishModal").find('.remodal-close').on('click' , function(){
                $('#publishModal').find('iframe').attr("src",blank_page_url);
                remodalInst_pub.close();
            });
        }
        return false;
    });
    //承認
    $('.link_waiting').on('click' , function(){
        var tid = $(this).data("id");
        var load_url = $(this).data("link");
        if(tid) {
            $('#publishModal').find('iframe').attr("src",load_url);
            remodalInst_pub.open();

            //close event set
            $("#publishModal").find('.remodal-close').off('click');
            $("#publishModal").find('.remodal-close').on('click' , function(){
                $('#publishModal').find('iframe').attr("src",blank_page_url);
                remodalInst_pub.close();
            });
        }
        return false;
    });
    $(document).on('closed', '#publishModal', function (e) {
        $('#publishModal').find('iframe').attr("src",blank_page_url);
    });

    //ソート入れ替え
    var sort_change_flg = 0;
    $(".sort-link a").on('click' , function(){
        var id = $(this).data('id');
        var sortchange_type = $(this).data('sortchange-type');

        if(id && sortchange_type && !sort_change_flg) {
            sort_change_flg = 1;
            $.ajaxSetup({
                data: {
                    '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>'
                }
            });
            $.ajax({
                type: 'POST',
                //data:param,
                data : {
                    sort_change_id : id,
                    sort_change_type : sortchange_type
                },
                url: '<?php echo $sort_change_url; ?>',
                dataType: 'json',//テキストとして受け取る
                success: function(result){
                    //処理成功時の動作
                    window.location.reload();
                },
                error : function(error, status, xhr) {
                    window.location.reload();
                }
            });
        }

        return false;
    });



});

function publishModalClose(reload) {
    $('#publishModal').find('iframe').attr("src",blank_page_url);
    if(reload == true) {
        window.location.reload();
    } else {
        remodalInst_pub.close();
    }
}

</script>
