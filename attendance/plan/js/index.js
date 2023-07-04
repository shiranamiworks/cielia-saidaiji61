var arg = new Object;
var pair = location.search.substring(1).split('&');
for(var i = 0;pair[i]; i++) {

    var kv = decodeURI(pair[i]).split('[]=');
    console.log(kv);
    if (!arg[kv[0]]) {
        arg[kv[0]] = [];
    }

    if (arg[kv[0]].length == 0) {
        arg[kv[0]] = [kv[1]];
    } else {
        arg[kv[0]].push(kv[1]);
    }
}

var initTags = function () {
    $('.tag-item').each(function () {
        for (var key in arg) {
            for (var i = 0; i < arg[key].length; i++) {
                if (key + '-' + arg[key][i] == $(this).data('tag')){
                    $(this).addClass('selected');
                }
            }
        }

    });
};

(function () {

    $(document).on('click', '.tag-item', function () {
        $(this).toggleClass('selected');
    });

    $(document).on('click', '.btn-drillDown', function () {
        var url = '';
        var l = $('.tag-item.selected').length;
        for (var i = 0; i < l; i++){
            var tagData = $('.tag-item.selected:eq('+i+')').data('tag');
            var tagName = tagData.split('-')[0];
            var tagValue = tagData.split('-')[1];

            if (url == '') {
                url += '?';
            } else {
                url += '&';
            }

            url += tagName + '[]=' + tagValue;
        }
        url = encodeURI(url);
        location.href = location.protocol + '//' + location.host + location.pathname + url;
    });

    $(document).on('click', '.btn-openWindow', function (e) {
        var openWinUrl = $(this).attr('href');
        window.open(openWinUrl , 'win1' , 'width='+TorikagoSimulatorWinSizeW+',height='+TorikagoSimulatorWinSizeH+',toolbar=no,location=no,menubar=no,scrollbars=yes');
        e.preventDefault();
    });
})();