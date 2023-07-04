;(function($) {

  $ = $ || {};
  $.app = $.app || {};

  $.app = function(){
    var _self = arguments.callee;
    if(_self.instance == null){
        _self.instance = this;
    }
    return _self.instance;
  };

  $.app.prototype = {

    strNoValue : ' --- ',

    param : {
      inputArea : '#inputArea .form-group',
      rangeSlideSelector : '[data-rangeslider]',
      inputItems : {
        'tsuki'   :'月々の支払額', 
        'bounas'  :'ボーナス' , 
        'atama'   :'頭金' , 
        'nensyu'  :'世帯年収' , 
        'kinri'   :'金利' , 
        'kikan'   :'返済期間' 
      },
      outputSelector : {
        'kakaku'  : '.result-price__sales .price-value dt',
        'kariire' : '.result-price__loan .price-value dt',
        'hempi'   : '.result-price__ratio .price-value dt'
      },
      //cookie保持パス
      cookiePath : '',
      kakakuLock : false
    },

    preData : {},

    //頭金の変動額
    pAtama : '',

    //range値の監視用
    rangeChkInterval : null,

    init : function() {

      //var keys = {}
      $(this.param.inputArea).each(function(){

        //var k = $(this).find('label.control-label').text();
        var $formInput = $(this).find('input.form-input');
        //var v = $formInput.attr('name');
        //keys[v] = k;

        var min = $formInput.attr('data-min');
        var max = $formInput.attr('data-max');
        var step = $formInput.attr('step');
        var inital = $formInput.attr('data-inital');

        var rangeParam = {
          'min'   : min,
          'max'   : max,
          'step'  : step,
          'value' : inital
        };
        $(this).find('.input-range').attr( rangeParam );
      });

      $.cookie.json = true;

      this.setRangeSlider();
      this.paramCheck();
      this.setSubmitEvent();
      this.setInputEvent();

    },

    setRangeSlider : function() {
      var scope = this;
      var $element = $(this.param.rangeSlideSelector);

      // Basic rangeslider initialization
      $element.rangeslider({

          // Deactivate the feature detection
          polyfill: false,

          // Callback function
          onInit: function() {
             scope.rangeValueSync(this.$element[0]);
             $('.pbar-inner span').show();
          }
          /*,

          // Callback function
          onSlide: function(position, value) {
              console.log('onSlide');
              console.log('position: ' + position, 'value: ' + value);
          },

          // Callback function
          onSlideEnd: function(position, value) {
              console.log('onSlideEnd');
              console.log('position: ' + position, 'value: ' + value);
          }
          */
      });
    },

    rangeValueSync : function(element) {
      var $obj = $(element);
      var val = $obj.val();
      //console.log('rangeValSync',val);
      var output_target = $obj.data('output-target');
      $(this.param.inputArea).find('.input-'+output_target).val(val);
    },

    inputValueSync : function(element) {
      var $obj = $(element);
      var val = $obj.val();
      //console.log('inputValSync',val);
      this.unsetInputEvent();
      var $inputRange = $(this.param.rangeSlideSelector, $obj.parents('.form-inline'));
      $inputRange.val(val).change();
      this.setInputEvent();
    },

    setInputEvent : function() {
      var scope = this;
      
      this.unsetInputEvent();

      $(document).on('input', 'input[type="range"], ' + this.param.rangeSlideSelector, function(e) {
          
          scope.rangeValueSync(e.target);
          var key = $(this).data('output-target');
          if(scope.kakakuLock == true) {
            console.log('========= kakakuLockCalc =========');
            scope.kakakuLockCalculation(key);
          }
      });

      //Androidバグ対策
      $(document).on('focus', '.form-input', function(e) {
        if($('html.android').length) {
          //$('.g-submit-btn').hide();
          //$('.head-content').hide();
          //$('.g-wrap').addClass('active');
        }
      });

      $(document).on('blur', '.form-input', function(e) {
          
          var val = $(this).val();
          var key = $(this).attr('name');

          var check = scope.inputCheck( key , val );
          if(check.error === true) {
            val = check.val;
            $(this).val( val );
          }

          scope.inputValueSync(e.target);
          if(scope.kakakuLock == true) {
            console.log('========= kakakuLockCalc =========');
            scope.kakakuLockCalculation(key);
          }
          
      });
    },

    unsetInputEvent : function() {
      $(document).off('input', 'input[type="range"], ' + this.param.rangeSlideSelector);
      $(document).off('blur', '.form-input');
      $(document).off('focus', '.form-input');
    },

    getUrlParam : function(key) {

      var url = location.search.substr(1).split("&");
      var paramObj = {};
      for(i=0; url[i]; i++) {
        var k = url[i].split('=');
        var paramKey = k[0];
        var paramValue = k[1];
        var escVal = this.esc( paramValue );

        paramObj[paramKey] = escVal;

      }

      if(Object.keys(paramObj).length) {

        if(typeof key !== 'undefined') {
          if( key in paramObj ) {
            return paramObj[key];
          }else{
            return '';
          }
        }
        return paramObj;
      }

      return {};
    },

    paramCheck : function() {
      var scope = this;
      var urlparam = this.getUrlParam();
      var calcFlag = false;
      var keyLists = $.extend(true, {}, this.param.inputItems);
      if(urlparam) {
        $.each(urlparam , function(paramKey , paramValue){

          if(paramKey == 'heya' || paramKey == 'kakaku' || paramKey == 'lk') {
            //var hidden = $('<input/>').attr({'type':'hidden','name':paramKey,'value':paramValue});
            //$('form').append(hidden);
            scope.formHiddenSet(paramKey , paramValue);

          }else if( paramKey == 'send' ) {
            //計算フラグを立てる
            calcFlag = true;
          }else{
            if(paramKey in scope.param.inputItems) {
              delete keyLists[paramKey];
              scope.setValue(paramKey , paramValue);
            }
          }

        });
      }

      //GET値がない項目に初期値をセット
      if(Object.keys(keyLists).length > 0) {
        $.each(keyLists , function(key , val){
          scope.setInitValue(key);
        });
      }

      //価格固定モードON・OFF判定
      if( 'lk' in urlparam && $('input[name=kakaku]').length ) {
        var bukkenKakaku = $('input[name=kakaku]').val();
        if(this.isNumber( bukkenKakaku ) && bukkenKakaku > 0) {
          
          this.kakakuLock = true;
          this.setPreData();
          bukkenKakaku = Math.floor(bukkenKakaku);
        }
      }

      //価格固定モードの場合は「販売価格」と表示
      //それ以外は「購入可能金額」と表示
      if( this.kakakuLock == true ) {
        $('.result-price__sales').find('.price-ttl').text('販売価格');
      }else{
        $('.result-price__sales').find('.price-ttl').text('購入可能金額');
      }


      if(calcFlag === true) {

        this.calculation();

      }else{
        
        if( this.kakakuLock == true ) {
            //物件価格を固定した計算実行
            this.noValueOutput();
            $(this.param.outputSelector.kakaku).html( this.numberFormat( bukkenKakaku ) );
            this.kakakuLockCalculation('lk_mode');
        }else{
          //計算処理なし
          this.noValueOutput();
        }
      }

    },

    //項目に指定値セット
    setValue : function(key , val) {

      var check = this.inputCheck(key , val);

      if( check.error === true ) {
        val = check.val;
        if(check.error_type == 'decimel' && key == 'tsuki') {
          $(this.param.inputArea).find('input[name='+key+']').attr('data-origval',check.origval);
        }
      }

      $(this.param.inputArea).find('input[name='+key+']').val(val);

      var $inputRange = $(this.param.rangeSlideSelector, $('input[name='+key+']').parents('.form-inline'));
      $inputRange.val(val).change();
    },

    //項目に初期値セット
    setInitValue : function(key) {

      var $item = $("input[name="+key+"]");
      var initalValue = $item.data('inital');

      //前回の入力値が保存されていれば
      var strage = this.getStrage();
      
      if(strage){
        if(key in strage) {
          initalValue = strage[key];
        }
      }

      this.setValue( key , initalValue );

    },

    setPreData : function() {
      var inputData = {};
      $.each(this.param.inputItems , function(key , val){
        inputData[key] = $("input[name="+key+"]").val();
      });
      this.preData = inputData;
      return;
    },

    displayError : function(errorMessage) {
      alert(errorMessage);
      /*
      $('#modalErrorDesc').html(errorMessage);
      var remodalInst = this.getRemodalInst('modalError');
      remodalInst.open();

      return remodalInst;
      */
    },


    //フォーム送信
    setSubmitEvent : function() {
      var scope = this;
      $('.btn-submit').on('click' , function(){

        //バリデーション
        var errorMessage = scope.validation();
        if( errorMessage ) {
        
          scope.displayError( errorMessage );

          $.each(this.preData , function(key , val){
            scope.setValue(key , val);
          });

          return false;
        
        }else{
          //入力値保存
          scope.saveStrage();
        }

        /* 
        if($("input[name=tsuki]").attr('data-origval')) {
          var tsuki_origval =("input[name=tsuki]").attr('data-origval');
          scope.formHiddenSet('tsuki' , tsuki_origval);
        }
        */

        //ここで物件価格や借入金額を一旦算出し、URLパラメータで飛ばすようにする
        var result = scope.calculation( true );
        if(result) {
          $.each(result , function(key , val) {
            if(key == 'kakaku' && scope.kakakuLock == true && val != $('input[name=kakaku]').val()) {
              console.log('val1',val);
              console.log('val0',$('input[name=kakaku]').val());
              //alert('kakakuerror');
              return false;
            }
            if(key == 'hempi' && val == scope.strNoValue) {
              val = '';
            }
            scope.formHiddenSet(key , val);
          });
        }

        $('form').submit();
        return false;
      });
    },


    formHiddenSet : function(key , val) {
      if($('input[name='+key+']').length) {
        $('input[name='+key+']').val(val);
      }else{
        var hidden = $('<input/>').attr({'type':'hidden','name':key,'value':val});
        $('form').append(hidden);
      }
    },

    //フォーム入力チェック
    validation : function() {
      var scope = this;
      var errorLabels = [];
      $.each(scope.param.inputItems , function(key , val){
        var val = $("input[name="+key+"]").val();

        //入力値のチェック
        var check = scope.inputCheck(key , val);
        if(check.error === true) {
          errorLabels.push( scope.param.inputItems[key] );
        }
      });

      if(errorLabels.length > 0) {
        return errorLabels.join(' , ') + 'にエラーがあります.';
      }

      return '';
    },

    inputCheck : function(key , val) {
      var error = false;
      var error_type = '';
      var origval = val;
      var $item = $("input[name="+key+"]");
      var min = $item.data('min');
      var max = $item.data('max');
      var decimel = $item.data('decimel');

      if(val === '') {
        error = true;
        error_type = 'empty';
        val = min;
      }else if( this.isNumber(val) === false ) {
        error = true;
        error_type = 'notNum';
        val = min;
      }else if(val < min) {
        error = true;
        error_type = 'min';
        val = min;
      }else if(val > max) {
        error = true;
        error_type = 'max';
        val = max;
      }

      //小数点の桁数チェック
      if(typeof decimel !== 'undefined') {

        var len = this.getDecimalPointLength(val);
        if(len > decimel) {
          error = true;
          error_type = 'decimel';
          //小数点桁数をdecimelの桁数でカット
          val = Math.floor( val * Math.pow( 10, decimel ) ) / Math.pow( 10, decimel ) ;
        }
      }else{
        if( !this.isInteger(val) ) {
          console.log(key);
          error = true;
          error_type = 'notInt';
          val = Math.floor( val );
        }
      }

      return { 'error' : error , 'error_type' : error_type , 'val' : val , 'origval' : origval };

    },

    //入力値の保存 
    saveStrage : function() {
      var scope = this;
      var saveValues = {};
      $.each(scope.param.inputItems , function(key , val){
        if(key != 'tsuki') {
          saveValues[key] = $("input[name="+key+"]").val();
        }
      });
      $.cookie( 'simulatorInputData', saveValues, { path: this.param.cookiePath } );
    },

    getStrage : function() {
      var scope = this;
      var strageValue = $.cookie( 'simulatorInputData' );
      return strageValue;
    },

    //計算なし結果表示
    noValueOutput : function() {

      $(this.param.outputSelector.kakaku).text( this.strNoValue );

      $(this.param.outputSelector.kariire).text( this.strNoValue );

      $(this.param.outputSelector.hempi).text( this.strNoValue );

    },

    //計算処理
    calculation : function( getResult ) {

      var scope = this;
      var input = {};
      $.each(scope.param.inputItems , function(key , val){
        input[key] = $("input[name="+key+"]").val();
      });

      var calc = new $.calc();
      calc.init(input);

      //借入金額
      if(this.kakakuLock == true && input['atama'] !== '' && $('input[name=kakaku]').val() !== '') {
        var kakaku_kariire = $('input[name=kakaku]').val() - input['atama'];
      }else{
        var kakaku_kariire = calc.getKariire();
      }

      //物件価格
      var kakaku_bukken = calc.getBukkenKakaku( kakaku_kariire );
      //返済比率
      var hensai_hiritsu = calc.getHensaiHiritsu();

      //結果をアウトプットせずに返すだけの場合
      if(typeof getResult !== 'undefined') {
        return {
          'kakaku' : kakaku_bukken,
          'kariire' : kakaku_kariire,
          'hempi' : hensai_hiritsu
        };
      }

      //結果表示
      $(this.param.outputSelector.kakaku).html( this.numberFormat( kakaku_bukken ) );

      $(this.param.outputSelector.kariire).html( this.numberFormat( kakaku_kariire ) );

      $(this.param.outputSelector.hempi).text( hensai_hiritsu );

    },

    //物件価格が固定の場合の計算処理
    kakakuLockCalculation : function(activeKey) {

      var scope = this;
      var input = {};
      var maxValues = {};
      
      clearInterval(this.rangeChkInterval);
      
      $.each(scope.param.inputItems , function(key , val){
        input[key] = $("input[name="+key+"]").val();
        maxValues[key] = $("input[name="+key+"]").data('max');
      });

      var calc = new $.calc();
      calc.init(input);

      console.log('calc_start---------');
      //var kakaku_bukken = $(this.param.outputSelector.kakaku).text();
      var kakaku_bukken = $('input[name=kakaku]').val();
      console.log( 'Number' , kakaku_bukken );

      var result = calc.lockModeCalc(kakaku_bukken , activeKey , input , maxValues , this.pAtama);

      console.log('app_input' , input);
      console.log('app_activeKey' ,activeKey);
      console.log('app_result' ,result);
      console.log('calc_end---------');
      this.unsetInputEvent();

      if(result) {
        $.each(result , function(key , val){
          scope.setValue(key , val);
        });
      }else{
        console.log('pre',this.preData);
        //エラーなら前回の入力値にもどす
        $.each(this.preData , function(key , val){
          scope.setValue(key , val);
        });

        this.rangeChkInterval = setInterval( function() {
          console.log('correct');
          scope.inputValueSync( '.input-'+activeKey );
          clearInterval(scope.rangeChkInterval);

        }, 500);
      }

      if(this.pAtama === '' || activeKey == 'atama') {
        this.pAtama = input['atama'];
      }

      this.setPreData();
      this.setInputEvent();
    },

    esc : function(val) {
      var val = $('<div />').text(val).html();
      return val;
    },

    isNumber : function(val){
      //実数チェック
      var pattern = /^([1-9]\d*|0)(\.\d+)?$/;
      return pattern.test(val);
    },

    isInteger : function(val) {
      var pattern = /^([1-9]\d*|0)$/;
      return pattern.test(val);
    },

    //小数点桁数取得
    getDecimalPointLength : function(val) {
      var numbers = String(val).split('.');
      var result  = 0;
 
      if (numbers[1]) {
          result = numbers[1].length;
      }
 
      return result;
    },

    /*
    //小数点を指定した桁数でカット
    cutDecimelPoint : function(val , point) {
      var len = this.getDecimalPointLength(val);
      if(len > point) {
        val = Math.floor( val * Math.pow( 10, point ) ) / Math.pow( 10, point ) ;
      }
      return val;
    },
    */

    //数値にカンマ付ける
    addComma : function(num) {
        var _num = num.replace( /^(-?\d+)(\d{3})/, "$1,$2" );
        if(_num !== num) {
          return this.addComma(_num);
        }
        return _num;
    },

    numberFormat : function(val){
        return String(val).replace( /(\d)(?=(\d\d\d)+(?!\d))/g, '$1,');
    }

  };


  //Entryキー無効
  $("input"). keydown(function(e) {
      if ((e.which && e.which === 13) || (e.keyCode && e.keyCode === 13)) {
          return false;
      } else {
          return true;
      }
  });

  //CSS調整
  //$('.g-wrap').css('paddingBottom',$('.head-content').height()+'px');
  $('.head-content').css('height' , $('.head-content').height()+'px');
  $('.g-wrap').css('height' , $('.g-wrap').height() - $('.head-content').height()+'px');
  $('.g-submit-btn').css('height' , $('.g-submit-btn').height()+'px');

  //計算ボタンの出現・消える処理
  var targetElm = $('.g-submit-btn'),
  winHeight = $('.g-wrap').height(),
  delayHeight = $('.g-wrap').offset().top - winHeight + $('.head-content').height();

  $('.g-wrap').on('load scroll resize',function(){
        var setThis = $('.input-area-bottom'),
        elmTop = setThis.offset().top,
        elmHeight = setThis.height(),
        scrTop = $('.g-wrap').scrollTop();
        
        //console.log('.g-wrap',$('.g-wrap').offset().top);
        //console.log('scrTop',scrTop);
        //console.log('elmTop',elmTop);
        //console.log('elmHeight',elmHeight);
        //console.log('winHeight',winHeight);
        //console.log('elmTop - winHeight',elmTop - winHeight);
        //if (scrTop > elmTop - winHeight + delayHeight && scrTop < elmTop + elmHeight){
        if (scrTop > elmTop - winHeight + delayHeight){
            targetElm.css('bottom',$(targetElm).height() * -1 +'px'); // 【上】からスクロールしてきた時のイベント
        //} else if (scrTop < elmTop - winHeight + delayHeight && scrTop < elmTop + delayHeight){
        } else if (scrTop < elmTop - winHeight + delayHeight){
            targetElm.css('bottom','0px'); // 【下】からスクロールしてきた時のイベント
        }
  });

 

})(jQuery);
