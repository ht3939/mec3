//"""""""""""""""""""""""""""""""""//
//                                 //
//	作成者：東出                      //
//	最終更新日時：2012/08/27 12:00    //
//                                 //
//"""""""""""""""""""""""""""""""""//

//===================================DOM生成時に実行====================================//
$(document).ready(function(){

		var monthry_total = $(".js-side-monthry").text();
		monthry_total = parseInt(monthry_total.replace( ",", "" ));

		function init(){
			$('select[name^="i_sim_num_normal"]').each( function(){

				var no = $(this).attr("data");
				var no_num = no.replace(/no-/g, "");
				if( $(this).val() != "0" ){
					$("#sim-normal"+no_num).parents("li").addClass('active');
				}
			} );

			$('select[name^="i_sim_num_micro"]').each( function(){

				var no = $(this).attr("data");
				var no_num = no.replace(/no-/g, "");
				if( $(this).val() != "0" ){
					$("#sim-micro"+no_num).parents("li").addClass('active');
				}
			} );

			$('select[name^="i_sim_num_nano"]').each( function(){

				var no = $(this).attr("data");
				var no_num = no.replace(/no-/g, "");
				if( $(this).val() != "0" ){
					$("#sim-nano"+no_num).parents("li").addClass('active');
				}
			} );

			$('input[name^="contract"]:checked').each( function (){
				$(this).parents("li").addClass('active');
				$("#"+$(this).val()).show();
			});

			$(".js-mnp.active").parents("dd").find(".js-efo-item").each(function(){
				var items = $(this).attr("name");
				REQUIRED_ITEMS.push(items);
			});

			var sms_param = ( $(".cart-option-input").parents("dl.cart-sms-option").find("input[name=sms-option]").is(":checked") ) ? "ok" : "ng" ;
			var ip_param = ( $(".cart-option-input").parents("dl.cart-sms-option").find("input[name=one-option-ip]:checked").is(":checked") ) ? "ok" : "ng" ;
			var pocket_param = ( $(".cart-option-input").parents("dl.cart-sms-option").find("input[name=one-option-pocket]:checked").is(":checked") ) ? "ok" : "ng" ;
			var secure_param = ( $(".cart-option-input").parents("dl.cart-sms-option").find("input[name=one-option-secure]:checked").is(":checked") ) ? "ok" : "ng" ;
			var param = param + "&option_sms="+sms_param;

			sidePriceChange( sms_param, ip_param, pocket_param ,secure_param);

			Validation();

			itemValidation();
		}

		init();

		var changeCart = function( param ){
			$.ajax({
				type : "POST",
				url  : "/server/itemCart.php",
				data : param,
				beforeSend : function(){

				},
				success : function( res ){
				},
				error : function(){
					console.log("error");
				},
			});
		}

		$("select[name^=i_sim_num]").change(function(){
			var no = $(this).attr("data");
			var no_num = no.replace(/no-/g, "");

			var sim_num = $("input[name^=sim_num][data="+no+"]").val();

			var sim_normal_num = $(this).parents('ul').find("select[name^=i_sim_num_normal][data="+no+"]").val();
			var sim_normal = ( sim_normal_num ) ? sim_normal_num : 0 ;
			var sim_micro_num = $(this).parents('ul').find("select[name^=i_sim_num_micro][data="+no+"]").val();
			var sim_micro = ( sim_micro_num ) ? sim_micro_num : 0 ;
			var sim_nano_num = $(this).parents('ul').find("select[name^=i_sim_num_nano][data="+no+"]").val();
			var sim_nano = ( sim_nano_num ) ? sim_nano_num : 0 ;
			var sim_total = Number(sim_normal) + Number(sim_micro) + Number(sim_nano);

			var param = "name_id="+$(this).parents('ul').find("[name=name_id]").val();
			var param = param + "&normal_num="+sim_normal;
			var param = param + "&micro_num="+sim_micro;
			var param = param + "&nano_num="+sim_nano;

			var param = param + "&item_type=sim";

			changeCart(param);


			if(sim_normal_num != 0 ){
				$("#sim-normal"+no_num).parents("li").addClass('active');
			}else{
				$("#sim-normal"+no_num).parents("li").removeClass('active');
			}

			if(sim_micro_num != 0 ){
				$("#sim-micro"+no_num).parents("li").addClass('active');
			}else{
				$("#sim-micro"+no_num).parents("li").removeClass('active');
			}

			if(sim_nano_num != 0 ){
				$("#sim-nano"+no_num).parents("li").addClass('active');
			}else{
				$("#sim-nano"+no_num).parents("li").removeClass('active');
			}

			if(sim_num == sim_total) {
				$(".error-"+no).hide();

			}else{
				$(".error-"+no).show();
			}

			itemValidation();
		});

		$('.cart-sim-list').find("li").on( "click" , function(){
			if( !$(this).hasClass('active') ){
				$('.cart-sim-list').find("li").removeClass('active');
				$(this).addClass('active');

				var no = $(this).find("input").attr("data");
				var no_num = no.replace(/no-/g, "");
				var type = $(this).find("input").val();

				$("select[name^=i_sim_num]").each( function(){
					$(this).val(0);
				});

				$("select[name=i_sim_num_"+type+no_num+"]").val(1);

				var sim_normal_num = $("select[name^=i_sim_num_normal][data="+no+"]").val();
				var sim_normal = ( sim_normal_num ) ? sim_normal_num : 0 ;
				var sim_micro_num = $("select[name^=i_sim_num_micro][data="+no+"]").val();
				var sim_micro = ( sim_micro_num ) ? sim_micro_num : 0 ;
				var sim_nano_num = $("select[name^=i_sim_num_nano][data="+no+"]").val();
				var sim_nano = ( sim_nano_num ) ? sim_nano_num : 0 ;

				var param = "name_id="+$("[name=name_id][data="+no+"]").val();
				var param = param + "&normal_num="+sim_normal;
				var param = param + "&micro_num="+sim_micro;
				var param = param + "&nano_num="+sim_nano;

				var param = param + "&item_type=sim";

				changeCart(param);

				itemValidation();
			}
		});

		$('input[name^="contract"]').change(function(){

			$(this).parents("dd").find("li").removeClass('active');
			$(this).parents("li").addClass('active');

			$(this).parents("dd").find('[class^=cart-contract-box]').hide();
			$('#'+$(this).val()).fadeIn();

			REQUIRED_ITEMS = new Array();

			$(".js-mnp.active").parents("dd").find(".js-efo-item").each(function(){
				var items = $(this).attr("name");
				REQUIRED_ITEMS.push(items);
			});

			Validation();

			var param = "name_id="+$(this).parents("dd").find("[name=name_id]").val();
			var param = param + "&number=" + $(this).parents("dd").find("[name=number]").val();
			var param = param + "&type=" + $(this).attr("data");
			var param = param + "&mnp_tel=" + $(this).parents("dd").find("input.input-tel").val();
			var param = param + "&mnp_contact=" + $(this).parents("dd").find("input.input-contact").val();
			var param = param + "&mnp_reserv=" + $(this).parents("dd").find("input.input-reserv").val();
			//var get_year = $(this).parents("dd").find("select.get-year").val();
			var get_year = "2015";
			var get_month = $(this).parents("dd").find("select.get-month").val();
			var get_day = $(this).parents("dd").find("select.get-day").val();
			var param = param + "&mnp_get_day=" + get_year +"年"+ get_month +"月"+ get_day+"日";
			var param = param + "&mnp_name=" + $(this).parents("dd").find("input.input-kanji").val();
			var param = param + "&mnp_kana=" + $(this).parents("dd").find("input.input-kana").val();

			var param = param + "&item_type=sp";

			changeCart(param);

			itemValidation();
		});

		$(".cart-option-input").on("click", function(){

			var param = "name_id="+$(this).parents("dl.cart-sms-option").find("[name=name_id]").val();

			var sms_param = ( $(this).parents("dl.cart-sms-option").find("input[name=sms-option]").is(":checked") ) ? "ok" : "ng" ;
			var ip_param = ( $(this).parents("dl.cart-sms-option").find("input[name=one-option-ip]:checked").is(":checked") ) ? "ok" : "ng" ;
			var pocket_param = ( $(this).parents("dl.cart-sms-option").find("input[name=one-option-pocket]:checked").is(":checked") ) ? "ok" : "ng" ;
			var secure_param = ( $(this).parents("dl.cart-sms-option").find("input[name=one-option-secure]:checked").is(":checked") ) ? "ok" : "ng" ;
			var param = param + "&option_sms="+sms_param;
			var param = param + "&option_ip="+ip_param;
			var param = param + "&option_pocket="+pocket_param;
			var param = param + "&option_secure="+secure_param;

			var param = param + "&item_type=sim&param_type=option";

			sidePriceChange( sms_param, ip_param, pocket_param ,secure_param);


			changeCart(param);
		});

		//オプションを選んだ際の金額変更
		function sidePriceChange( sms, ip, pocket , secure){
			
			var monthry_total_side = monthry_total;
			monthry_total_side = ( sms == "ok" ) ? monthry_total_side + 150 : monthry_total_side;
			monthry_total_side = ( ip == "ok" ) ? monthry_total_side + 350 : monthry_total_side;
			monthry_total_side = ( pocket == "ok" ) ? monthry_total_side + 500 : monthry_total_side;
			monthry_total_side = ( secure == "ok" ) ? monthry_total_side + 500 : monthry_total_side;

			$(".js-side-monthry").text(commaPrice(monthry_total_side));
			var tax = Math.floor(monthry_total_side*0.08);
			$(".js-side-tax").text(commaPrice(tax));
			$(".js-side-total").text(commaPrice(monthry_total_side+tax));
		}

		function commaPrice(value) {
			var value = get_comma3_deleted(value);
			while (value != (value = value.replace(/^(-?\d+)(\d{3})/, "$1,$2")));
			if (isNaN(parseInt(value))) {
				value = "0";
			}
			return value;
		}

		function get_comma3_deleted(value) {
			value = "" + value;
			return value.replace(/^\s+|\s+$|,/g, "");
		}

		function numberFormat(str){
			var num = str.match(/\d/g).join("");
			return num;
		}

		function str_to_int(value){
			var num = value.match(/\d/g).join("");
			return num;
		}


		$("input[name^=i_tel], input[name^=i_contact]").keyup(function() {
			itemValidation();
		});

		function itemValidation(){

			var error_flag = false;

			$("select[name^=i_sim_num]").each(function( ) {
				var no = $(this).attr("data");

				var sim_num = $("input[name^=sim_num][data="+no+"]").val();

				var sim_normal_num = $(this).parents('ul').find("select[name^=i_sim_num_normal][data="+no+"]").val();
				var sim_normal = ( sim_normal_num ) ? sim_normal_num : 0 ;
				var sim_micro = $(this).parents('ul').find("select[name^=i_sim_num_micro][data="+no+"]").val();
				var sim_nano = $(this).parents('ul').find("select[name^=i_sim_num_nano][data="+no+"]").val();
				var sim_total = Number(sim_normal) + Number(sim_micro) + Number(sim_nano);

				var param = "name_id="+$(this).parents('ul').find("[name=name_id]").val();
				var param = param + "&normal_num="+sim_normal;
				var param = param + "&micro_num="+sim_micro;
				var param = param + "&nano_num="+sim_nano;

				var param = param + "&item_type=sim";

				if(sim_num != sim_total) error_flag = true;
			});

			$('input[name^="contract"]:checked').each( function(){

				var type = $(this).attr("data");
				
				if(type == "mnp"){
					var mnp_tel = $(this).parents("dd").find("input.input-tel").val();
					var mnp_contact = $(this).parents("dd").find("input.input-contact").val();
				}

				if(type){
					if(type == "mnp" && ((mnp_tel.length < 9  || mnp_tel.length > 13) || (mnp_contact.length < 9 || mnp_contact.length > 13))){
						error_flag = true;
					}
				}else{
					error_flag = true;
				}

			});


			if(error_flag == false){
				$(".cart-side-btn").unbind('click');
				$(".cart-side-btn").on("click", function(){

					$("[id^=mnp]").each( function() {
						var param = "name_id="+$(this).parents("dd").find("[name=name_id]").val();
						var param = param + "&number=" + $(this).parents("dd").find("[name=number]").val();
						var param = param + "&type=" + $(this).parents("dd").find("input[name^=contract]:checked").attr("data");
						var param = param + "&mnp_tel=" + $(this).parents("dd").find("input.input-tel").val();
						var param = param + "&mnp_contact=" + $(this).parents("dd").find("input.input-contact").val();
						var param = param + "&mnp_reserv=" + $(this).parents("dd").find("input.input-reserv").val();
						var get_year = "2015";
						var get_month = $(this).parents("dd").find("select.get-month").val();
						var get_day = $(this).parents("dd").find("select.get-day").val();
						var param = param + "&mnp_get_day=" + get_year +"年"+ get_month +"月"+ get_day+"日";
						var param = param + "&mnp_name=" + $(this).parents("dd").find("input.input-kanji").val();
						var param = param + "&mnp_kana=" + $(this).parents("dd").find("input.input-kana").val();

						var param = param + "&item_type=sp";

						changeCart(param);
					});
					$("#cart-form").submit();
				});
				$("#confirm_button").addClass('active');
			}else{
				$(".cart-side-btn").unbind('click');
				$("#confirm_button").removeClass('active');
			}
		}
	
		/* ボックスのCSS等指定 */
		// $("#drag").css({'position':'fixed','/position':'absolute','top':'40%','left':'10px','width':'180px','height':'33px','background':'#ff0000','padding':'5px','font-size':'12px','color':'#ffffff','line-height':'1.4','text-align':'center','border-radius':'10px','-webkit-border-radius':'10px','-moz-border-radius':'10px','opacity':TOOLTIP_OPACITY_RATE})
  //                 .draggable({'opacity':TOOLTIP_OPACITY_RATE});
		/* 一番上にある入力に不備がある必須項目名 */
		var top_required_name;

	//【EVENT ACTION】===================ページ読み込み時に必須項目が揃っているかチェック
		
		//-------------------------------不備数の数に応じてボックスや下部のsendボタンを書き換える
		Rewrite_Box( Validation() );	//ページリロード時
	
	//【EVENT ACTION】=================== バリデーションを走らせるイベント

		$("textarea,input:not(#confirm_button)").keyup(function(){ Rewrite_Box( Validation() ); });
		$("textarea,input:not(#confirm_button) , select").blur(function(){ Rewrite_Box( Validation() ); });
		$("textarea,input:not(#confirm_button) , select").change(function(){ Rewrite_Box( Validation() ); });
		$("textarea,input:not(#confirm_button) , select").focusout(function(){ Rewrite_Box( Validation( $(this).attr("name") ) ); });
		
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
		});
	
	
	//【EVENT ACTION】=================================================未入力がある時のボタン押下
	$(".efo-button").on("mouseup",function(e){
		Validation();
        var p = $("*[name="+ top_required_name +"]").offset().top;
        $('html,body').animate({ scrollTop: p }, 'fast');
		setTimeout(function(){ $("*[name="+ top_required_name +"]").trigger("focus"); } , 300);
		Validation(top_required_name);
		/* 電話番号への入力のケースで、input[text]を同時に点滅させる */
		var telFlag = false;
		for (var i = TEL_ARR.length - 1; i >= 0; i--) {
			if(top_required_name == TEL_ARR[i]) telFlag = true;
		};
		if(telFlag){
			for (var i = TEL_ARR.length - 1; i >= 0; i--) {
				Form_Flash(TEL_ARR[i]);
			};
		} else {
			Form_Flash(top_required_name);
		}
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
					// case "i_tel":
					// 	/*ここにtel固有の処理*/
					// 	if( $(SELECTER).val().length == 0 ){
					// 		$(SELECTER).css({"backgroundColor":FORM_BG_COLOR});
					// 		NotRequiredCount++;
					// 		if(tooltip_place_name == REQUIRED_ITEMS[i]) Display_Tooltip(tooltip_place_name,"未入力です");
					// 		required_text += "「" + $("#quoted_"+REQUIRED_ITEMS[i]).html() + "」";
					// 	}else if ( $(SELECTER).val().replace(/-/g,"").replace(/ー/g,"").replace(/―/g,"").replace(/－/g,"").replace(/‐/g,"").replace(/－/g,"").replace(/‐/g,"").match(/^[0-9０-９]+$/) == null ) {
     				//		$(SELECTER).css({"backgroundColor":FORM_BG_COLOR});
					// 		NotRequiredCount++;
					// 		if(tooltip_place_name == REQUIRED_ITEMS[i]) Display_Tooltip(tooltip_place_name,"数字またはハイフンのみ<br />入力してください。");
					// 		required_text += "「" + $("#quoted_"+REQUIRED_ITEMS[i]).html() + "」";
					// 	}else if ( $(SELECTER).val().replace(/-/g,"").replace(/ー/g,"").replace(/―/g,"").replace(/－/g,"").replace(/‐/g,"").replace(/－/g,"").replace(/‐/g,"").length < 10 ) {
     				//		$("*[name=i_tel1_1]").css({"backgroundColor":FORM_BG_COLOR});
     				//		$("*[name=i_tel1_2]").css({"backgroundColor":FORM_BG_COLOR});
   					//		$("*[name=i_tel1_3]").css({"backgroundColor":FORM_BG_COLOR});
					// 		NotRequiredCount++;
					// 		if(tooltip_place_name == REQUIRED_ITEMS[i]) Display_Tooltip(tooltip_place_name,"番号数が足りていません。");
					// 		required_text += "「" + $("#quoted_"+REQUIRED_ITEMS[i]).html() + "」";
					// 	}else{
					// 		$(SELECTER).css({ "backgroundColor":"#FFF" });
					// 		Delete_Tooltip(REQUIRED_ITEMS[i]);
					// 	}
					// 	break;
					case "i_tel1_1":
						/*ここにtel固有の処理*/
						if( $(SELECTER).val().length == 0 ){
							$(SELECTER).css({"backgroundColor":FORM_BG_COLOR});
							tel_error++;
							tel_error_flg = true;
							if(tooltip_place_name == REQUIRED_ITEMS[i]) Display_Tooltip(tooltip_place_name,"未入力です");
						}else if ( $(SELECTER).val().replace(/-/g,"").replace(/ー/g,"").replace(/―/g,"").replace(/－/g,"").replace(/‐/g,"").replace(/－/g,"").replace(/‐/g,"").match(/^[0-9０-９]+$/) == null ) {
        					$(SELECTER).css({"backgroundColor":FORM_BG_COLOR});
        					tel_error++;
        					tel_error_flg = true;
							if(tooltip_place_name == REQUIRED_ITEMS[i]) Display_Tooltip(tooltip_place_name,"数字またはハイフンのみ<br />入力してください。");
						}else if ( $(SELECTER).val().replace(/-/g,"").replace(/ー/g,"").replace(/―/g,"").replace(/－/g,"").replace(/‐/g,"").replace(/－/g,"").replace(/‐/g,"").length < 2 ) {
        					$(SELECTER).css({"backgroundColor":FORM_BG_COLOR});
        					tel_error++;
        					tel_error_flg = true;
							if(tooltip_place_name == REQUIRED_ITEMS[i]) Display_Tooltip(tooltip_place_name,"番号数が足りていません。");
						}else{
							$(SELECTER).css({ "backgroundColor":"#FFF" });
							Delete_Tooltip(REQUIRED_ITEMS[i]);
							tel_num_error++;
						}
						break;
					case "i_tel1_2":
						/*ここにtel固有の処理*/
						if( $(SELECTER).val().length == 0 ){
							$(SELECTER).css({"backgroundColor":FORM_BG_COLOR});
							tel_error++;
							tel_error_flg = true;
							if(tooltip_place_name == REQUIRED_ITEMS[i]) Display_Tooltip(tooltip_place_name,"未入力です");
						}else if ( $(SELECTER).val().replace(/-/g,"").replace(/ー/g,"").replace(/―/g,"").replace(/－/g,"").replace(/‐/g,"").replace(/－/g,"").replace(/‐/g,"").match(/^[0-9０-９]+$/) == null ) {
        					$(SELECTER).css({"backgroundColor":FORM_BG_COLOR});
        					tel_error++;
        					tel_error_flg = true;
							if(tooltip_place_name == REQUIRED_ITEMS[i]) Display_Tooltip(tooltip_place_name,"数字またはハイフンのみ<br />入力してください。");
						}else if ( $(SELECTER).val().replace(/-/g,"").replace(/ー/g,"").replace(/―/g,"").replace(/－/g,"").replace(/‐/g,"").replace(/－/g,"").replace(/‐/g,"").length < 2 ) {
        					$(SELECTER).css({"backgroundColor":FORM_BG_COLOR});
        					tel_error++;
        					tel_error_flg = true;
							if(tooltip_place_name == REQUIRED_ITEMS[i]) Display_Tooltip(tooltip_place_name,"番号数が足りていません。");
						}else{
							$(SELECTER).css({ "backgroundColor":"#FFF" });
							Delete_Tooltip(REQUIRED_ITEMS[i]);
							tel_num_error++;
						}
						break;
					case "i_tel1_3":
						/*ここにtel固有の処理*/
						if( $(SELECTER).val().length == 0 ){
							$(SELECTER).css({"backgroundColor":FORM_BG_COLOR});
							tel_error++;
							tel_error_flg = true;
							if(tooltip_place_name == REQUIRED_ITEMS[i]) Display_Tooltip(tooltip_place_name,"未入力です");
						}else if ( $(SELECTER).val().replace(/-/g,"").replace(/ー/g,"").replace(/―/g,"").replace(/－/g,"").replace(/‐/g,"").replace(/－/g,"").replace(/‐/g,"").match(/^[0-9０-９]+$/) == null ) {
        					$(SELECTER).css({"backgroundColor":FORM_BG_COLOR});
        					tel_error++;
        					tel_error_flg = true;
							if(tooltip_place_name == REQUIRED_ITEMS[i]) Display_Tooltip(tooltip_place_name,"数字またはハイフンのみ<br />入力してください。");
						}else if ( $(SELECTER).val().replace(/-/g,"").replace(/ー/g,"").replace(/―/g,"").replace(/－/g,"").replace(/‐/g,"").replace(/－/g,"").replace(/‐/g,"").length < 2 ) {
        					$(SELECTER).css({"backgroundColor":FORM_BG_COLOR});
        					tel_error++;
        					tel_error_flg = true;
							if(tooltip_place_name == REQUIRED_ITEMS[i]) Display_Tooltip(tooltip_place_name,"番号数が足りていません。");
						}else{
							$(SELECTER).css({ "backgroundColor":"#FFF" });
							Delete_Tooltip(REQUIRED_ITEMS[i]);
							tel_num_error++;
						}
						break;
//					case "i_mail":
// 					/*ここにmail固有の処理*/
//					break;
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
