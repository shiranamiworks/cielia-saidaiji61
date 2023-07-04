<script>

var remodal_options = { 'hashTracking' : false };
var remodalInst_del = null;
var remodalInst_pub = null;

$(function(){

    //ToolTip
    $('.tooltip-item').tooltip();

    remodalInst_del = $('[data-remodal-id=delModal]').remodal(remodal_options);
    remodalInst_pub = $('[data-remodal-id=publishModal]').remodal(remodal_options);


    //delete 
    $(".btn-delete").on('click' , function(){
      var t_data_id = $(this).data('id');
      var t_data_name = $(this).data('name');
      var id1_data_name = $("#accountID1Name").val();
      if(t_data_id) {
        $("#delModal").find('#delAccountName').text(t_data_name);
        $("#delModal").find('#deleteID').val(t_data_id);
        $("#delModal").find('#accountID1Name').text(id1_data_name);
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
});


</script>
