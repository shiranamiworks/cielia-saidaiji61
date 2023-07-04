<script>
var remodal_options = { 'hashTracking' : false };
var remodalInst_del = null;
var remodalInst_edit = null;

$(function(){
    
    //ToolTip
    $('[data-toggle="tooltip"] , .tooltip-item').tooltip();

    remodalInst_del = $('[data-remodal-id=delModal]').remodal(remodal_options);
    remodalInst_edit= $('[data-remodal-id=editModal]').remodal(remodal_options);

    //edit button
    $(".btn-edit").on('click' , function(){

        var t_data_id = $(this).data('id');
        var t_data_name = $(this).data('name');
        $("#editModal").find('#inputCategoryName').val(t_data_name);
        $("#editModal").find('#editID').val(t_data_id);

        remodalInst_edit.open();

        //close event set
        $("#editModal").find('.remodal-close').off('click');
        $("#editModal").find('.remodal-close').on('click' , function(){
            remodalInst_edit.close();
        });

        $("#editModal").find("#btnEdit").off('click');
        $("#editModal").find("#btnEdit").on('click' , function(){
            if($("#editModal").find("#inputCategoryName").val()) {
                $("#editForm").submit();
            } else {
                alert("カテゴリ名を入力してください");
            }
        });

        return false;
    });

    //delete button
    $(".btn-delete").on('click' , function(){
        var t_data_id = $(this).data('id');
        var t_data_name = $(this).data('name');
        var id1_data_name = $("#catID1Name").val();
        if(t_data_id) {
            $("#delModal").find('#delCategoryName').text(t_data_name);
            $("#delModal").find('#deleteID').val(t_data_id);
            $("#delModal").find('#categoryID1Name').text(id1_data_name);
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
});

</script>