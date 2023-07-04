<script>

function openfileSelectWindow(type)
{
    var filelist_url = '<?= $filelist_url ?>';
    if(type == 1 || type == 'url') select_url += "?urlset=1";
    //$("#srcimg").val("d");
    OpenNewWindow('win1',filelist_url,'width=930,height=700,toolbar=no,location=no,menubar=no,scrollbars=yes');
    
}

</script>