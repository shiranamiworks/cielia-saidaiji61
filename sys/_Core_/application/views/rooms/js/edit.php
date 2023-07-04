<script>

$(function(){
  //価格タイプが万円台の場合は、価格の入力値が十万・一万の位がゼロかチェック
  $('#editForm').on('submit',function(){

    var error = '';
    var pricetype = $('input[name="pricetype"]:checked').val();
    var priceval = $('input[name=price]').val();
    if(pricetype == 2 && priceval !== '') {
      priceval = Number( priceval );
      if( Math.round(priceval) !== priceval ){
        error = '価格は半角整数で入力してください。';
      }else{
        if( priceval < 100) {
          error = '価格は100以上で入力してください。';
        }else{
          var _v1 = String(priceval).substr(-1 , 1);
          var _v2 = String(priceval).substr(-2 , 1);
          if(_v1 != 0 || _v2 != 0) {
            error = '価格タイプが「万円台」の場合は、十万・一万の位の値を0にしてください';
          }
        }
      }

      if(error) {
        alert(error);
        return false;
      }
    }

  });
});

</script>
