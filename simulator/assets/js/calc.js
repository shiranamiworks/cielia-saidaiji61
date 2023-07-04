;(function($) {

  $ = $ || {};
  $.calc = $.calc || {};
  $.calc = function(){
    var _self = arguments.callee;
      if(_self.instance == null){
          _self.instance = this;
      }
      return _self.instance;
  };


  $.calc.prototype = {
    strNoValue : ' --- ',

    inputValue : {
      'tsuki' : 0,
      'bounas' : 0,
      'atama' : 0,
      'nensyu' : 0, 
      'kinri' : 0,
      //返済期間
      'kikan' : 0
    },

    init : function(args) {
      var scope = this;
      $.each(args , function(key , val) {
        scope.setInputValue( key , val );
      });
    },

    setInputValue : function( key , val ) {
      this.inputValue[key] = parseFloat( val );
    },

    //100万円あたりの返済額（月々）
    getTsukiHensaigaku : function() {
      var result = this.pmt( this.inputValue.kinri/100/12 , this.inputValue.kikan*12 , 1000000) * -1;
      return Math.round(result);
    },
    //100万円あたりの返済額（ボーナス）
    getBounasHensaigaku : function() {
      var result = this.pmt( this.inputValue.kinri/100/2 , this.inputValue.kikan*2 , 1000000) * -1;
      return Math.round(result);
    },

    //借入れ金額計算
    getKariire : function() {
      var tsukiHensai = this.getTsukiHensaigaku();
      var bounasHensai = this.getBounasHensaigaku();

      var res1 =  (this.inputValue.tsuki / tsukiHensai) * 1000000;
      var res2 =  (this.inputValue.bounas / bounasHensai) * 1000000;
      
      return this.roundDown(res1 + res2);
    },

    //物件価格計算
    getBukkenKakaku : function( kariire ) {
      //借入れ金額 + 頭金
      return ( kariire + this.inputValue.atama );
    },

    //返済比率計算
    getHensaiHiritsu : function() {
      var nenHensai = this.inputValue.tsuki*12 + this.inputValue.bounas*2;

      if(!this.inputValue.nensyu) {
        return this.strNoValue;
      }
      //比率 パーセンテージ
      var result = ( nenHensai ) ? ( nenHensai / this.inputValue.nensyu * 100 ) : 0;
      
      if(result) {
        //少数第2位で四捨五入
        result = result * 100;
        result = Math.round(result);
        result = result / 100;
        return result.toFixed(2);
      }

      return 0;
    },

    //価格ロックモードでの計算
    lockModeCalc : function(lockKakaku , activeKey , input , maxValues , pAtama) {
      
      var scope = this;

      $.each(input , function(key , val) {
        input[key] = parseFloat( val );
        scope.setInputValue( key , val );
      });

      //------------------------------------------------------
      //月々の支払い額を変更した場合
      //------------------------------------------------------
      if(activeKey == 'tsuki') {

        var kariire = this.getKariire();
        //物件価格
        var bukkenKakaku = this.getBukkenKakaku(kariire);
        //物件価格の差
        var diff = lockKakaku - bukkenKakaku;

        var atama = input['atama'] + diff;
        /*
        if(atama > maxValues['atama'] || atama < 0) {
          return false;
        }
        */

        var atamaDiff = 0;
        if(pAtama !== ''){
          var atamaDiff = pAtama - atama;
        }
        console.log('atamaDiff',pAtama,atama,atamaDiff);

        console.log('tsuki',lockKakaku , bukkenKakaku , diff);

        //価格が増えている場合
        if(diff < 0) {
          
          //期間を1年減らして物件価格取得
          //----------------------------------------------------
          if(input['kikan'] > 1) {
            scope.setInputValue( 'kikan' , input['kikan']-1 );
          }else{
            return false;
          }
        //価格が減っている場合
        }else if(diff > 0){

          //期間を1年増やして物件価格取得
          //----------------------------------------------------
          if(input['kikan'] < maxValues['kikan']) {
            scope.setInputValue( 'kikan' , input['kikan']+1 );
          }else{
            return false;
          }
        }

        var _kariire = this.getKariire();
        var _rest = kariire - _kariire;

        console.log('_rest',_rest);
        var kikanInc = 0;
        if(_rest) {
          kikanInc = Math.floor( Math.abs(atamaDiff) / Math.abs(_rest) );
        }
        if( kikanInc >= 1) {
          if(diff < 0) {
            kikanInc *= -1;
          }
        }else{
          kikanInc = 0;
        }

        if((input['kikan'] + kikanInc) < 1) kikanInc = 0;
        if((input['kikan'] + kikanInc) > 35) kikanInc = 0; 

          console.log('kikanInc',kikanInc);
        /*
        //物件価格
        var _bukkenKakaku = this.getBukkenKakaku(_kariire);

        var _rest = kariire - _kariire;
        var _diff = lockKakaku - _bukkenKakaku;
        */
/*
        if(Math.abs(_rest) < Math.abs(atamaDiff)) {
          scope.setInputValue( 'atama' , pAtama );
          _bukkenKakaku = this.getBukkenKakaku(_kariire);
          _diff = lockKakaku - _bukkenKakaku;
        }
        */
        /*
        var kikanInc = Math.floor( Math.abs(_diff) / Math.abs(_rest) );
        console.log('_bukkenKakaku' , _bukkenKakaku);
        console.log('kikanInc',_diff , _rest , kikanInc);
        if( kikanInc >= 1) {
          if(diff < 0) {
            kikanInc *= -1;
          }
        }else{
          kikanInc = 0;
        }
        */

        scope.setInputValue( 'kikan' , (input['kikan'] + kikanInc) );

        //期間を調整後、再度物件価格を計算して、端数を頭金で調整する 
        var __kariire = this.getKariire();
        var __bukkenKakaku = this.getBukkenKakaku(__kariire);

        if(lockKakaku != __bukkenKakaku) {
          var atamaInc = lockKakaku - __bukkenKakaku;
          console.log('atamaInc',atamaInc);
          scope.setInputValue( 'atama' , input['atama'] + atamaInc );
        }

        if( (input['atama'] + atamaInc) > maxValues['atama'] || (input['atama'] + atamaInc) < 0) {
          return false;
        }

        return this.inputValue;


      //------------------------------------------------------
      //その他の項目を変更した場合
      //------------------------------------------------------
      }else{

        var tsukiHensai = this.getTsukiHensaigaku();
        var bounasHensai = this.getBounasHensaigaku();


        if(lockKakaku < this.inputValue.atama) {
          return false;
        }

        var tsukiVal = ( ( lockKakaku - this.inputValue.atama ) - this.roundDown( (this.inputValue.bounas / bounasHensai) * 1000000 ) ) * tsukiHensai / 1000000;

        if(tsukiVal < 0) {
          return false;
        }

        //小数点第2を切り捨て
        /*
        var _tsukiVal = tsukiVal*10;
        if(activeKey == 'lk_mode') {
          tsukiVal = Math.ceil(_tsukiVal)/10;
        }else{
          tsukiVal = Math.floor(_tsukiVal)/10;
        }
        */

        if( tsukiVal > maxValues['tsuki'] ) {
        
          tsukiVal = maxValues['tsuki'];

        }
        this.setInputValue( 'tsuki' , tsukiVal );

        //頭金を調整
        var kakakuKariire = this.getKariire();
        console.log('kakakuKariire1',kakakuKariire);
        console.log('lockKakaku',lockKakaku);
        var atama = lockKakaku - kakakuKariire;

        if(atama > 0) {
          
          if(atama > maxValues['atama']) {
            atama = maxValues['atama'];
          }

          this.setInputValue( 'atama' , atama );
        
        }

        return this.inputValue;

      }
    },


    pmt : function(rate_per_period, number_of_payments, present_value, future_value, type) {
      
      future_value = typeof future_value !== 'undefined' ? future_value : 0;
      type = typeof type !== 'undefined' ? type : 0;

      if(rate_per_period != 0.0){
          // Interest rate exists
          var q = Math.pow(1 + rate_per_period, number_of_payments);
          return -(rate_per_period * (future_value + (q * present_value))) / ((-1 + q) * (1 + rate_per_period * (type)));
      } else if(number_of_payments != 0.0){
          // No interest rate, but number of payments exists
          return -(future_value + present_value) / number_of_payments;
      }
   
      return 0;

    },

    /*
    pmt : function( i, n , pv, fv, type ) {
      fv = typeof fv !== 'undefined' ? fv : 0;
      type = typeof type !== 'undefined' ? type : 0;

      return ( (-pv-fv*Math.pow(1+i, -n))*i ) / ( ( 1+i*type )*( 1-Math.pow(1+i, -n) ) );
    },
    */

    //一の位で切り捨て
    roundDown : function(val) {
      var _val = val/10;
      return Math.floor(_val) * 10;
    }
  };

})(jQuery);
