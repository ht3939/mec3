//"""""""""""""""""""""""""""""""""//
//                                 //
//	作成者：東出                      //
//	最終更新日時：2012/08/27 12:00    //
//                                 //
//"""""""""""""""""""""""""""""""""//

//===================================DOM生成時に実行====================================//
$(document).ready(function(){

		/* 一番上にある入力に不備がある必須項目名 */
		var top_required_name;

		function init(){

//			Validation();

			itemValidation();
		}

		init();

		var changeCart = function( param ){
			$.ajax({
				type : "POST",
				url  : $("input[name=BASE_URL]").val()+"API/cart.php",
				data : param,
				async: false,
				beforeSend : function(){

				},
				success : function( res ){
				},
				error : function(){
					console.log("error");
				},
			});
		}

		//SIMサイズ変更イベント
		$("select[name^=i_sim_size]").change(function(){
			itemValidation();
		});
		//MNPを希望する電話番号変更イベント
		$("input[name^=i_tel]").change(function(){
			itemValidation();
		});
		$("input[name^=i_tel]").on("keyup",function(){
			itemValidation();
		});
		//MNP予約番号変更イベント
		$("input[name^=i_reserv]").change(function(){
			itemValidation();
		});
		$("input[name^=i_reserv]").on("keyup",function(){
			itemValidation();
		});
		//MNP番号取得日(年)変更イベント
		$("select[name^=i_year]").change(function(){
			itemValidation();
		});
		//MNP番号取得日(月)変更イベント
		$("select[name^=i_month]").change(function(){
			itemValidation();
		});
		//MNP番号取得日(日)変更イベント
		$("select[name^=i_day]").change(function(){
			itemValidation();
		});
		//MNP元のキャリア変更イベント
		$("select[name^=i_carrier]").change(function(){
			itemValidation();
		});
		//ご契約内容変更イベント
		$("input[name^=i_contract]").change(function(){
			itemValidation();
		});

		//サブミットイベント
		$(".js-submit").on("click", function(){
			$("[id^=js-product-list-item-sp]").each( function() {
				var param = "name_id="+$(this).find("input[name^=name_id]").val();
				var param = param + "&pay=" + $(this).find("select[name^=i_payment_method]").val();

				var param = param + "&item_type=sp";
				var param = param + "&func=item";
				changeCart(param);
			});
			$("[id^=js-product-list-item-sim]").each( function() {
				var param = "name_id="+$(this).find("input[name^=name_id]").val();
				var param = param + "&sim_size=" + $(this).find("select[name^=i_sim_size]").val();
				var param = param + "&contract=" + $(this).find("input[name^=i_contract]:checked").val();
				var param = param + "&type=" + $(this).find("input[name^=i_contract]:checked").attr("data");
				var param = param + "&mnp_tel=" + $(this).find("input[name^=i_tel]").val();
				var param = param + "&mnp_reserv=" + $(this).find("input[name^=i_reserv]").val();
				var param = param + "&mnp_year=" + $(this).find("select[name^=i_year]").val();
				var param = param + "&mnp_month=" + $(this).find("select[name^=i_month]").val();
				var param = param + "&mnp_day=" + $(this).find("select[name^=i_day]").val();
				var param = param + "&mnp_carrier=" + $(this).find("select[name^=i_carrier]").val();
				var param = param + "&item_type=sim";

				//オプション
				var sms_param = ( $(this).find("input[name^=i_opt_sms]").is(":checked") ) ? "ok" : "ng" ;
				var security_param = ( $(this).find("input[name^=i_opt_security]").is(":checked") ) ? "ok" : "ng" ;
				var guarantee_param = ( $(this).find("input[name^=i_opt_guarantee]").is(":checked") ) ? "ok" : "ng" ;
				var support_param = ( $(this).find("input[name^=i_opt_support]").is(":checked") ) ? "ok" : "ng" ;
				var param = param + "&option_sms="+sms_param;
				var param = param + "&option_ip="+security_param;
				var param = param + "&option_pocket="+guarantee_param;
				var param = param + "&option_secure="+support_param;
				var param = param + "&func=item";
				changeCart(param);
			});
			$("#cart-form").submit();
		});

		function itemValidation(){
			var error_flag = false;
			var arrName = "";
			top_required_name = "";

			//SIMサイズ選択
			$('select[name^="i_sim_size"]').each( function(){
				if($(this).val() == 0){
					error_flag = true;
					// 一番上にあるまだ入力に不備がある必須項目の名前を取得
					if(top_required_name == "") top_required_name = $(this).attr("name");
				}
			});
			//MNPを希望する電話番号
			$("input[name^=i_tel]").each(function(){
				arrName = $(this).attr("name").split("-");
				if($("input[name='i_contract-"+arrName[1]+"']:checked").val() == 1){
					if ( $(this).val().replace(/-/g,"").replace(/ー/g,"").replace(/―/g,"").replace(/－/g,"").replace(/‐/g,"").replace(/－/g,"").replace(/‐/g,"").match(/^[0-9０-９]+$/) == null ) {
						error_flag = true;
						if($(this).hasClass('_textbox--required') === false){
    						$(this).addClass('_textbox--required');
    					}
						// 一番上にあるまだ入力に不備がある必須項目の名前を取得
						if(top_required_name == "") top_required_name = $(this).attr("name");
					}else if ( $(this).val().replace(/-/g,"").replace(/ー/g,"").replace(/―/g,"").replace(/－/g,"").replace(/‐/g,"").replace(/－/g,"").replace(/‐/g,"").length != 11 ) {
						error_flag = true;
						if($(this).hasClass('_textbox--required') === false){
    						$(this).addClass('_textbox--required');
    					}
						// 一番上にあるまだ入力に不備がある必須項目の名前を取得
						if(top_required_name == "") top_required_name = $(this).attr("name");
					}
					else{
    					$(this).removeClass('_textbox--required');
					}
				}
			});
			//MNP予約番号
			$("input[name^=i_reserv]").each(function(){
				arrName = $(this).attr("name").split("-");
				if($("input[name='i_contract-"+arrName[1]+"']:checked").val() == 1){
					if($(this).val().length != 10){
						error_flag = true;
						if($(this).hasClass('_textbox--required') === false){
    						$(this).addClass('_textbox--required');
    					}
						// 一番上にあるまだ入力に不備がある必須項目の名前を取得
						if(top_required_name == "") top_required_name = $(this).attr("name");
					}
					else if($(this).val().match(/^[0-9０-９]+$/) == null){
						error_flag = true;
						if($(this).hasClass('_textbox--required') === false){
    						$(this).addClass('_textbox--required');
    					}
						// 一番上にあるまだ入力に不備がある必須項目の名前を取得
						if(top_required_name == "") top_required_name = $(this).attr("name");
					}
					else{
    					$(this).removeClass('_textbox--required');
					}
				}
			});
			//MNP番号取得日(年)
			$('select[name^="i_year"]').each( function(){
				arrName = $(this).attr("name").split("-");
				if($(this).val() == 0 && $("input[name='i_contract-"+arrName[1]+"']:checked").val() == 1){
					error_flag = true;
					if($(this).hasClass('_pulldown-menu--required') === false){
						$(this).addClass('_pulldown-menu--required');
					}
					// 一番上にあるまだ入力に不備がある必須項目の名前を取得
					if(top_required_name == "") top_required_name = $(this).attr("name");
				}
				else{
					$(this).removeClass('_pulldown-menu--required');
				}
			});
			//MNP番号取得日(月)
			$('select[name^="i_month"]').each( function(){
				arrName = $(this).attr("name").split("-");
				if($(this).val() == 0 && $("input[name='i_contract-"+arrName[1]+"']:checked").val() == 1){
					error_flag = true;
					if($(this).hasClass('_pulldown-menu--required') === false){
						$(this).addClass('_pulldown-menu--required');
					}
					// 一番上にあるまだ入力に不備がある必須項目の名前を取得
					if(top_required_name == "") top_required_name = $(this).attr("name");
				}
				else{
					$(this).removeClass('_pulldown-menu--required');
				}
			});
			//MNP番号取得日(日)
			$('select[name^="i_day"]').each( function(){
				arrName = $(this).attr("name").split("-");
				if($(this).val() == 0 && $("input[name='i_contract-"+arrName[1]+"']:checked").val() == 1){
					error_flag = true;
					if($(this).hasClass('_pulldown-menu--required') === false){
						$(this).addClass('_pulldown-menu--required');
					}
					// 一番上にあるまだ入力に不備がある必須項目の名前を取得
					if(top_required_name == "") top_required_name = $(this).attr("name");
				}
				else{
					$(this).removeClass('_pulldown-menu--required');
				}
			});
			//MNP元のキャリア
			$('select[name^="i_carrier"]').each( function(){
				arrName = $(this).attr("name").split("-");
				if($(this).val() == 0 && $("input[name='i_contract-"+arrName[1]+"']:checked").val() == 1){
					error_flag = true;
					if($(this).hasClass('_pulldown-menu--required') === false){
						$(this).addClass('_pulldown-menu--required');
					}
					// 一番上にあるまだ入力に不備がある必須項目の名前を取得
					if(top_required_name == "") top_required_name = $(this).attr("name");
				}
				else{
					$(this).removeClass('_pulldown-menu--required');
				}
			});

			if(error_flag == false){
				$("#"+BTNTXT_COMPLETE).css("display","inline");
				$("#"+BTNTXT_NOTICE).css("display","none");
			}else{
				$("#"+BTNTXT_NOTICE).css("display","inline");
				$("#"+BTNTXT_COMPLETE).css("display","none");
			}
		}
	
		/* ボックスのCSS等指定 */
		// $("#drag").css({'position':'fixed','/position':'absolute','top':'40%','left':'10px','width':'180px','height':'33px','background':'#ff0000','padding':'5px','font-size':'12px','color':'#ffffff','line-height':'1.4','text-align':'center','border-radius':'10px','-webkit-border-radius':'10px','-moz-border-radius':'10px','opacity':TOOLTIP_OPACITY_RATE})
  //                 .draggable({'opacity':TOOLTIP_OPACITY_RATE});

	//【EVENT ACTION】===================ページ読み込み時に必須項目が揃っているかチェック
		
		//-------------------------------不備数の数に応じてボックスや下部のsendボタンを書き換える
		Rewrite_Box( itemValidation() );	//ページリロード時
//		Rewrite_Box( Validation() );	//ページリロード時
	
	//【EVENT ACTION】=================== バリデーションを走らせるイベント

/*
		$("textarea,input:not(#confirm_button)").keyup(function(){ Rewrite_Box( Validation() ); });
		$("textarea,input:not(#confirm_button) , select").blur(function(){ Rewrite_Box( Validation() ); });
		$("textarea,input:not(#confirm_button) , select").change(function(){ Rewrite_Box( Validation() ); });
		$("textarea,input:not(#confirm_button) , select").focusout(function(){ Rewrite_Box( Validation( $(this).attr("name") ) ); });
*/		
		// 住所補完用にツールチップを表示
		$("*[name="+ SIDE_TOOLTIP_ZIP_NAME +"]").focus(function(){ Display_Tooltip(SIDE_TOOLTIP_ZIP_NAME,"郵便番号を入力すると<br />自動で住所が入力されます。"); });
		$("*[name="+ SIDE_TOOLTIP_ZIP_NAME +"]").blur(function(){ Delete_Tooltip(SIDE_TOOLTIP_ZIP_NAME); });
		
		// textareaにあらかじめサンプルテキストを表示
		if( $("#drag").length != 0 ){
			if( $("*[name="+ TEXTAREA_COMMENT_NAME +"]").val() == "" ||  $("*[name="+ TEXTAREA_COMMENT_NAME +"]").val() == TEXTAREA_COMMENT ){
				$("*[name="+ TEXTAREA_COMMENT_NAME +"]").val(TEXTAREA_COMMENT).css("color","#969696");
			}
		}
		$("*[name="+ TEXTAREA_COMMENT_NAME +"]").focus(function(){ 
			if(this.value == TEXTAREA_COMMENT){ 
				$(this).val("").css("color","#000"); 
			} 
		});
		$("*[name="+ TEXTAREA_COMMENT_NAME +"]").blur(function(){ 
			if(this.value == ""){ 
				$(this).val(TEXTAREA_COMMENT).css("color","#969696"); 
			} 
			if(this.value != TEXTAREA_COMMENT){ 
				$(this).css("color","#000"); } 
		});

		// フォームがズレた場合に吹き出しを再表示する
		$("form input:not([type=text])").click(function(){
			// 1ms後に処理しないと他のクリックイベントの後に処理されない
			setTimeout(function(){ 
				for( var i=0;i < REQUIRED_ITEMS.length;i++ ){
					// 現在,吹き出しを表示しているものにしか再表示を行わせない
					if ( $("#efo_balloon_" + REQUIRED_ITEMS[i] +":visible").length != 0 ) Validation(REQUIRED_ITEMS[i]);
				}
			}, 1);
		});
	//【EVENT ACTION】==========================================================ページ離脱時
		
		var execBeforeUnload = true;
		//【ACTION】----------- form の submit 時に beforeunload が発生するのでキャンセルする
		$("form").bind("submit", function(e){ CancelBeforeUnload(); } );
		
		//【ACTION】------------------- 画面閉じた、または更新したときのイベント(Opera未対応)
		$(window).bind('beforeunload', function(e) {
/*
			if (execBeforeUnload) {
				// 未入力、入力完了で文章変更
				var count = Validation();
				if ( $("#efo").size() > 0 ) {
					//message = "未入力項目があります。";
					message = "未入力項目はあと"+count+"項目です。\n再度お申込の場合にも入力データは保存されておりません。\n";
				}else if($("#drag").size() == 0) {
					// 確認画面用
					message = "[上記の内容で送信]をクリックすると申込が完了します。";
				}else{
					message = "未入力項目はありません。";
				}
				// Chrome,Safariだけ別文章
				if (!jQuery.support.checkOn) return "このページから移動します。よろしいですか？\n" + message;
				else return message;

			}
*/
		});
	
	
	//【EVENT ACTION】=================================================未入力がある時のボタン押下
	$(".efo-button").on("mouseup",function(e){
//		Validation();
		itemValidation();
        var p = $("*[name="+ top_required_name +"]").offset().top;
        $('html,body').animate({ scrollTop: p }, 'fast');
		setTimeout(function(){ $("*[name="+ top_required_name +"]").trigger("focus"); } , 300);
//		Validation(top_required_name);
		Form_Flash(top_required_name);
		return false;
	});
	
	//【EVENT ACTION】============================================================送信ボタン押下
	$("#confirm_button").on("mouseup",function(){
		CancelBeforeUnload();
		var temp = $("*[name="+ TEXTAREA_COMMENT_NAME +"]").val() + "";
		if( temp == TEXTAREA_COMMENT ){
	
		
			$("*[name="+ TEXTAREA_COMMENT_NAME +"]").val("");
		}
	
		$("#confirm_button").trigger("click");
		return false;
	});
	
	//【EVENT ACTION】====================================未入力項目がありますボタンホバーで効果
	$("#efo , #efo_box ,#confirm_button").on("mouseover",function(){ $(this).css({opacity:0.7}); })
						.on("mouseout",function(){ $(this).css({opacity:1.0}); } );
	
	//【EVENT ACTION】==================================================郵便番号を半角にして,住所補間
	$("*[name="+ SIDE_TOOLTIP_ZIP_NAME +"]").on("keyup",function(){
		FulltoHalf(SIDE_TOOLTIP_ZIP_NAME);
		AjaxZip3.zip2addr(this,'',ZIP_INTERPOLATION_ARRAY[0],ZIP_INTERPOLATION_ARRAY[1],'dummy',ZIP_INTERPOLATION_ARRAY[2]);	// 住所補間
		return;
	});
	
	//【FUNCTION】====================================================入力項目が正常に入っているかチェック
	function Validation(tooltip_place_name){	// 引数にはtooltipを吐き出す要素のnameプロパティを指定か、空か
		
		var NotRequiredCount = 0;
		var tel_error_flg = false;
		var required_text = "";
		top_required_name = "";
		var tel_total_num = 0;
		var tel_error = 0;
		var tel_num_error = 0;
		
		
		for( var i=0;i < REQUIRED_ITEMS.length;i++ ){
			// CSSセレクタの定義
			var SELECTER = "*[name="+REQUIRED_ITEMS[i]+"]";
			
			// セレクトボックスの場合特別処理
			if ($(SELECTER).attr("type") == "select-one"){
				// value=0(未選択)の時だけ、背景色を変える
				if ($(SELECTER).val() == "0" ){
					$(SELECTER).css({ backgroundColor:FORM_BG_COLOR });
					NotRequiredCount++;
					if(tooltip_place_name == REQUIRED_ITEMS[i]) Display_Tooltip(tooltip_place_name,"未選択です");
					required_text += "「" + $("#quoted_"+REQUIRED_ITEMS[i]).html() + "」";
				}else{
					$(SELECTER).css({ backgroundColor:"#FFF" });
					Delete_Tooltip(REQUIRED_ITEMS[i]);
				}
			}
			else if ( $(SELECTER).attr("type") == "radio" ) {
				if ($("[name="+REQUIRED_ITEMS[i]+"]:checked").val()){
					$(SELECTER).css({ backgroundColor:"#FFF" });
					Delete_Tooltip(REQUIRED_ITEMS[i]);
				}else{
					$(SELECTER).css({ backgroundColor:FORM_BG_COLOR });
					NotRequiredCount++;
					if(tooltip_place_name == REQUIRED_ITEMS[i]) Display_Tooltip(tooltip_place_name,"未選択です");
					required_text += "「" + $("#quoted_"+REQUIRED_ITEMS[i]).html() + "」";
				}
			}
			else if( $(SELECTER).attr("type") == "checkbox" ){
				if( $(SELECTER).is(":checked") ){
					$(SELECTER).css({ backgroundColor:"#FFF" });
					Delete_Tooltip(REQUIRED_ITEMS[i]);
				}else{
					$(SELECTER).css({ backgroundColor:FORM_BG_COLOR });
					NotRequiredCount++;
					if(tooltip_place_name == REQUIRED_ITEMS[i]) Display_Tooltip(tooltip_place_name,"未選択です");
					required_text += "「" + $("#quoted_"+REQUIRED_ITEMS[i]).html() + "」";
				}
			}
			else{
				/*-------------------------------固有バリデーション処理*/
				switch(REQUIRED_ITEMS[i]){
					default:
						/*-------------------------------共通バリデーション処理 */
						if( $(SELECTER).val().length  < 9 || $(SELECTER).val().length  > 13){
							$(SELECTER).css({"backgroundColor":FORM_BG_COLOR});
							NotRequiredCount++;
							if(tooltip_place_name == REQUIRED_ITEMS[i]) Display_Tooltip(tooltip_place_name,"未入力です");
							required_text += "「" + $("#quoted_"+REQUIRED_ITEMS[i]).html() + "」";
						}else{
							$(SELECTER).css({ "backgroundColor":"#FFF" });
							Delete_Tooltip(REQUIRED_ITEMS[i]);
						}
						break;
				}
			}

			
			// 一番上にあるまだ入力に不備がある必須項目の名前を取得
			if(NotRequiredCount > 0 && top_required_name == "") top_required_name = REQUIRED_ITEMS[i];
			

		}
		
		return NotRequiredCount;
	}

	//【FUNCTION】====================================================BOXやボタンの表示を変える
	function Rewrite_Box(NotrequiredCount){

		return;
	}
	
	//【FUNCTION】=========================引数nameの要素の横にツールチップで引数テキストを表示
	function Display_Tooltip(name,text){
		if(TOOL_TIP == "TRUE"){
			// 吹き出しを表示する前に以前の吹き出しを消す(重複禁止処理)
			$("#efo_balloon_"+name ).remove();
			
			var targetOffset = $("*[name="+ name +"]").offset();
			var targetWidth  = $("*[name="+ name +"]").width();
			var balloonLeft = targetOffset.left + targetWidth + 5;
			var balloonTop = targetOffset.top+5;
			$("body").prepend("<div id='efo_balloon_"+name+"'><div>"+ text +"</div></div>");
			$("#efo_balloon_"+name ).css({
				left:balloonLeft,
				top:balloonTop,
				position:"absolute",
				borderLeft:"10px solid transparent",
				borderTop:"10px solid "+SIDE_TOOLTIP_COLOR
			});
			$("#efo_balloon_"+ name +" div").css({
				fontSize:"12px",
				minWidth:"50px",
				maxWidth:"200px",
				padding:"5px 10px",
				marginTop:"-20px",
				background:SIDE_TOOLTIP_COLOR,
				color:"#fff",
				borderRadius:"5px",
				position:"relative"
			});
			//$("#efo_balloon_"+name).draggable();
			
			// 吹き出しをフェードイン
			$("#efo_balloon_"+name).hide();
			if(!jQuery.support.style) $("#efo_balloon_"+name).show(); // IE6,7
			else $("#efo_balloon_"+name).fadeIn("slow"); // それ以外
		}
	}
	//【FUNCTION】========================================引数nameの要素の横のツールチップを消去
	function Delete_Tooltip(name){
		if(!jQuery.support.style) $("#efo_balloon_"+name).hide();	// IE6,7
		else $("#efo_balloon_"+name).fadeOut("slow"); // それ以外
		return;
	}
	//【FUNCTION】=================================================================例外処理対応
	function CancelBeforeUnload() {
		execBeforeUnload = false;
		setTimeout(function(){ execBeforeUnload = true;} , 100);
		return;
	}
	//【FUNCTION】=================================================================フォーム点滅
	function Form_Flash(required_name){
		setTimeout(function(){ $("*[name="+ required_name +"]").css({"backgroundColor":FORM_BG_FLASH_COLOR}); } , 500);
		setTimeout(function(){ $("*[name="+ required_name +"]").css({"backgroundColor":FORM_BG_COLOR}); } , 1000);
		setTimeout(function(){ $("*[name="+ required_name +"]").css({"backgroundColor":FORM_BG_FLASH_COLOR}); } , 1500);
		setTimeout(function(){ $("*[name="+ required_name +"]").css({"backgroundColor":FORM_BG_COLOR}); } , 2000);
		setTimeout(function(){ Rewrite_Box( Validation() ); } , 2050);
	}
	//【FUNCTION】=============================================全角から半角へコンバートする関数
	function FulltoHalf(targer_name){
		var full_number_array = new Array("０","１","２","３","４","５","６","７","８","９");
		var half_number_array = new Array("0","1","2","3","4","5","6","7","8","9");
		var count;
		var number = $("*[name="+ targer_name +"]").val();
		while(number.match(/[０-９]/)) {
			for(count=0;count < full_number_array.length; count++){
				number = number.replace( full_number_array[count] , half_number_array[count] );
			}
		}
		$("*[name="+ targer_name +"]").val(number);
		return;
	}
	//【LIBRARY】=================================================================カナ同時入力機能
		if(KANA_CONVERSION == "TRUE"){
			var ktxConstant = { letterType : { hiragana : 0, katakana : 1 },insertType : { auto : 0, check : 1, checked : 2, button : 3 } }; var kanaTextExtension = {target : [ [ KANA_CONVERSION_NAME[0], KANA_CONVERSION_NAME[1], ktxConstant.letterType.katakana, ktxConstant.insertType.auto ] ], conf : { labelStrHiragana : 'ふりがなを自動挿入する',labelStrKatakana : 'フリガナを自動挿入する',buttonStrHiragana : '名前からふりがなを挿入する',buttonStrKatakana : '名前からフリガナを挿入する',idBaseStr : 'kanaTextExtension_',timer : null,elmName : null,elmKana : null,convertFlag : false,baseKana : '',ignoreString : '',input : '',values : [],active : true, kanaExtractionPattern : new RegExp('[^ 　ぁあ-んー]', 'g'), kanaCompactingPattern : new RegExp('[ぁぃぅぇぉっゃゅょ]', 'g')	}, init : function() { var len = kanaTextExtension.target.length; for ( var i = 0; i < len; i++ ) { var nameStr = kanaTextExtension.target[i][0]; var kanaStr = kanaTextExtension.target[i][1]; var name = document.getElementsByName(nameStr); var kana = document.getElementsByName(kanaStr); if ( kanaTextExtension.target[i][3] == ktxConstant.insertType.check ) { kanaTextExtension.createCheckBox(name[0],false); } else if ( kanaTextExtension.target[i][3] == ktxConstant.insertType.checked ) { kanaTextExtension.createCheckBox(name[0],true); } else if ( kanaTextExtension.target[i][3] == ktxConstant.insertType.button ) { kanaTextExtension.createButton(name[0]); } kanaTextExtension.addEvent( name[0], 'focus', kanaTextExtension.ktxFocus ); kanaTextExtension.addEvent( name[0], 'keydown', kanaTextExtension.ktxKeyDown ); kanaTextExtension.addEvent( name[0], 'blur', kanaTextExtension.ktxBlur ); } }, ktxFocus : function( event ) { kanaTextExtension.conf.elmName = kanaTextExtension.getEventTarget(event); kanaTextExtension.conf.elmKana = kanaTextExtension.getCorrespondingElement(); kanaTextExtension.stateInput(); kanaTextExtension.ktxSetInterval(); }, ktxKeyDown : function() { if ( kanaTextExtension.conf.convertFlag ) { kanaTextExtension.stateInput(); } }, ktxBlur : function() { kanaTextExtension.ktxClearInterval(); }, ktxSetInterval : function() { kanaTextExtension.conf.timer = setInterval( kanaTextExtension.checkValue, 30 ); }, ktxClearInterval : function() { clearInterval(kanaTextExtension.conf.timer); }, stateInput : function() { if ( kanaTextExtension.getInsertType() != ktxConstant.insertType.button ) { kanaTextExtension.conf.baseKana = kanaTextExtension.conf.elmKana.value; } kanaTextExtension.conf.convertFlag = false; kanaTextExtension.conf.ignoreString = kanaTextExtension.conf.elmName.value; if ( kanaTextExtension.getInsertType() == ktxConstant.insertType.check || kanaTextExtension.getInsertType() == ktxConstant.insertType.checked ) { var checkbox = document.getElementById(kanaTextExtension.conf.idBaseStr + kanaTextExtension.conf.elmName.name); if ( checkbox && checkbox.checked ) { kanaTextExtension.conf.active = true; } else { kanaTextExtension.conf.active = false; } } else if ( kanaTextExtension.getInsertType() == ktxConstant.insertType.button ) { kanaTextExtension.conf.active = false; } else { kanaTextExtension.conf.active = true; } }, stateConvert : function() { kanaTextExtension.conf.baseKana = kanaTextExtension.conf.baseKana + kanaTextExtension.conf.values.join(''); kanaTextExtension.conf.convertFlag = true; kanaTextExtension.conf.values = []; }, stateClear : function() { kanaTextExtension.conf.baseKana = ''; kanaTextExtension.conf.convertFlag = false; kanaTextExtension.conf.ignoreString = ''; kanaTextExtension.conf.input = ''; kanaTextExtension.conf.values = []; }, checkValue : function() { var newInput = kanaTextExtension.conf.elmName.value; if ( newInput == '' ) { kanaTextExtension.stateClear(); kanaTextExtension.setKana();} else { newInput = kanaTextExtension.removeString(newInput); if ( kanaTextExtension.conf.input == newInput ) { return; } else { kanaTextExtension.conf.input = newInput; if ( !kanaTextExtension.conf.convertFlag ) { var newValues = newInput.replace(kanaTextExtension.conf.kanaExtractionPattern,'').split(''); kanaTextExtension.checkConvert(newValues); kanaTextExtension.setKana(newValues); } } } }, checkConvert : function( newValues ) { if ( !kanaTextExtension.conf.convertFlag ) { if ( Math.abs(kanaTextExtension.conf.values.length - newValues.length) > 1 ) { var tmpValues = newValues.join('').replace(kanaTextExtension.conf.kanaCompactingPattern,'').split(''); if ( Math.abs(kanaTextExtension.conf.values.length - tmpValues.length) > 1 ) { kanaTextExtension.stateConvert(); } } else { if ( kanaTextExtension.conf.values.length == kanaTextExtension.conf.input.length && kanaTextExtension.conf.values.join('') != kanaTextExtension.conf.input ) { kanaTextExtension.stateConvert(); } } } }, setKana : function( newValues ) { if ( !kanaTextExtension.conf.convertFlag ) { if( newValues ) { kanaTextExtension.conf.values = newValues; } if( kanaTextExtension.conf.active ) { kanaTextExtension.conf.elmKana.value = kanaTextExtension.toKatakana( kanaTextExtension.conf.baseKana + kanaTextExtension.conf.values.join('') ); } } }, toKatakana : function( src ) { if ( kanaTextExtension.getLetterType() == ktxConstant.letterType.katakana ) { var str = new String; for( var i=0; i<src.length; i++ ) { var c = src.charCodeAt(i); if ( kanaTextExtension.isHiragana(c) ) { str += String.fromCharCode(c + 96); } else { str += src.charAt(i); } } return str; } else { return src; } }, isHiragana : function( char ) { return ((char >= 12353 && char <= 12435) || char == 12445 || char == 12446); }, removeString : function( newInput ) { if ( newInput.match(kanaTextExtension.conf.ignoreString) ) { return newInput.replace(kanaTextExtension.conf.ignoreString,''); } else { var ignoreArray = kanaTextExtension.conf.ignoreString.split(''); var inputArray = newInput.split(''); var len = ignoreArray.length; for( var i=0; i<len; i++ ) { if ( ignoreArray[i] == inputArray[i] ) { inputArray[i] = ''; } } return inputArray.join(''); } }, createCheckBox: function( element, flag ) { var parent = element.parentNode; var div = kanaTextExtension.createBlock(); var checkbox = kanaTextExtension.createInputCheckbox(element, flag); var label = kanaTextExtension.createLabel(element); parent.replaceChild(div, element); div.appendChild(element); div.appendChild(checkbox); div.appendChild(label); }, createBlock : function() { var div = document.createElement('div'); div.style.margin = '0px'; div.style.padding = '0px'; div.style.display = 'inline'; return div; }, createInputCheckbox: function( element, flag ) { var input = document.createElement('input'); input.type = 'checkbox'; if ( element.id ) { input.id = element.id; } else { input.id = kanaTextExtension.conf.idBaseStr + element.name;	} input.checked = flag; input.style.border = 'none'; input.style.background = 'transparent'; input.style.cursor = 'pointer'; input.style.marginLeft = '5px'; return input; }, createLabel: function( element ) { var label = document.createElement('label'); if ( element.id ) { label.htmlFor = element.id; } else { label.htmlFor = kanaTextExtension.conf.idBaseStr + element.name; } label.style.cursor = 'pointer'; if ( !kanaTextExtension.getLetterType(element) ) { label.innerHTML = kanaTextExtension.conf.labelStrHiragana; } else { label.innerHTML = kanaTextExtension.conf.labelStrKatakana; } return label; }, createButton: function( element ) { var parent = element.parentNode; var div = kanaTextExtension.createBlock(); var button = kanaTextExtension.createInputButton(element); parent.replaceChild(div, element); div.appendChild(element); div.appendChild(button); }, createInputButton: function( element ) { var input = document.createElement('input'); input.type = 'button'; if ( element.id ) { input.id = element.id; } else { input.id = kanaTextExtension.conf.idBaseStr + element.name; } input.style.margin = '0px'; input.style.marginLeft = '5px'; if ( !kanaTextExtension.getLetterType(element) ) { input.value = kanaTextExtension.conf.buttonStrHiragana; } else { input.value = kanaTextExtension.conf.buttonStrKatakana; } input.onclick = function() { if ( kanaTextExtension.conf.elmName ) { if ( this.id == (kanaTextExtension.conf.idBaseStr + kanaTextExtension.conf.elmName.name) ) { kanaTextExtension.conf.elmKana.value = kanaTextExtension.toKatakana( kanaTextExtension.conf.baseKana + kanaTextExtension.conf.values.join('') ); } } }; return input; }, getCorrespondingElement : function() { var result = null; var element = kanaTextExtension.conf.elmName; if ( element ) { var len = kanaTextExtension.target.length; for ( var i = 0; i < len; i++ ) { if ( element.name.match(kanaTextExtension.target[i][0]) ) { result = document.getElementsByName(kanaTextExtension.target[i][1]); result = result[0]; break; } } } return result; }, getLetterType : function( element ) { var result = 0; if ( !element ) { element = kanaTextExtension.conf.elmName; } var len = kanaTextExtension.target.length; for ( var i = 0; i < len; i++ ) { if ( element.name.match(kanaTextExtension.target[i][0]) ) { result = kanaTextExtension.target[i][2]; break; } } return result; }, getInsertType : function( element ) { var result = 0; if ( !element ) { element = kanaTextExtension.conf.elmName; } var len = kanaTextExtension.target.length; for ( var i = 0; i < len; i++ ) { if ( element.name.match(kanaTextExtension.target[i][0]) ) { result = kanaTextExtension.target[i][3]; break; } } return result; }, getEventTarget : function( event ) { var element = null; if ( event && event.target ) { element = event.target; } else if ( window.event && window.event.srcElement ) { element = window.event.srcElement; } return element; }, getTargetElements : function( tag, cls ) { var elements = new Array(); var targetElements = document.getElementsByTagName(tag.toUpperCase()); var len = targetElements.length; for ( var i = 0; i < len; i++ ) { if ( targetElements[i].className.match(cls) ) { elements[elements.length] = targetElements[i]; } } return elements; }, addEvent : function( target, event, func ) { try { target.addEventListener(event, func, false); } catch (e) { target.attachEvent('on' + event, func); } } }
			kanaTextExtension.addEvent( window, 'load', kanaTextExtension.init );
		}
	//【LIBRARY】============================================================テキストエリア動的拡大
	/* jQuery autoResize (textarea auto-resizer) @copyright James Padolsey http://james.padolsey.com @version 1.04 */
		(function(a){a.fn.autoResize=function(j){var b=a.extend({onResize:function(){},animate:true,animateDuration:150,animateCallback:function(){},extraSpace:20,limit:1000},j);this.filter('textarea').each(function(){var c=a(this).css({resize:'none','overflow-y':'hidden'}),k=c.height(),f=(function(){var l=['height','width','lineHeight','textDecoration','letterSpacing'],h={};a.each(l,function(d,e){h[e]=c.css(e)});return c.clone().removeAttr('id').removeAttr('name').css({position:'absolute',top:0,left:-9999}).css(h).attr('tabIndex','-1').insertBefore(c)})(),i=null,g=function(){f.height(0).val(a(this).val()).scrollTop(10000);var d=Math.max(f.scrollTop(),k)+b.extraSpace,e=a(this).add(f);if(i===d){return}i=d;if(d>=b.limit){a(this).css('overflow-y','');return}b.onResize.call(this);b.animate&&c.css('display')==='block'?e.stop().animate({height:d},b.animateDuration,b.animateCallback):e.height(d)};c.unbind('.dynSiz').bind('keyup.dynSiz',g).bind('keydown.dynSiz',g).bind('change.dynSiz',g)});return this}})(jQuery);
		
		$("*[name="+ TEXTAREA_COMMENT_NAME +"]").autoResize({
	    // On resize:
	    onResize : function() {
	        $(this).css({opacity:0.8});
	    },
	    // After resize:
	    animateCallback : function() {
	        $(this).css({opacity:1});
	    },
	    // Quite slow animation:
	    animateDuration : 500,
	    // More extra space:
	    extraSpace : 10
		});
	//【LIBRARY】============================================================formの2重送信防止
		/* jQuery Disable On Submit Plugin http://www.evanbot.com/article/jquery-disable-on-submit-plugin/13 Copyright (c) 2009 Evan Byrne (http://www.evanbot.com) */
		$.fn.disableOnSubmit = function(disableList){ if(disableList == null){var $list = 'input[type=submit],input[type=button],input[type=reset],button';} else{var $list = disableList;} $(this).find($list).removeAttr('disabled'); $(this).submit(function(){$(this).find($list).attr('disabled','disabled');}); return this; };
		$('form').disableOnSubmit();
});
