<?php
$dir = dirname($_SERVER["SCRIPT_NAME"]);
?>

$(function(){

    var $container = $('.searchTag-container');

    $.ajax({
        url : '<?= $dir ?>/rooms/tags',
        dataType : 'html',
        cache : false
    }).done(function(data) {
        $container.empty();
        $container.html(data);
        initTags();
    }).fail(function(data) {
        alert('データの取得に失敗しました。')
    });

});
