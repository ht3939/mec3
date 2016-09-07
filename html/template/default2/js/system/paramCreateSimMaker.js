//絞り込み条件をクリックしたときの処理
$( function(){
	$("#side-left li, .js-order, .js-disp").on( "click", function( e ){

		e.preventDefault();

		//現在の絞り込み条件の部分は除く
		var delete_flag = false;
		if( $(this).parents("ul").hasClass('js-delete') ){
			delete_flag = true;
		}

		var now_url = location.href;

		//パラメータ配列(優先順)
		var param_array = [
				"mvno",
				"communication",
				"cardsort",
				"cardprice",
				"carddisplay"
			];

		// if( now_url.indexOf( "?" ) != -1 ){
		if( true ){
			//URLにパラメータがある場合
			//hiddenタグからパラメータを取得
			var now_param = [];
			for( var i=0; i<param_array.length; i++ ){
				now_param[param_array[i]] = $("#param-"+param_array[i]).val().split(",");
			}

			//クリックされたパラメータを取得
			if( delete_flag == false ){
				var click_param = "";
				var click_data = "";
				if( $(this).hasClass('js-order') || $(this).hasClass('js-disp') ){
					click_param = $(this).parent("ul").attr("data");
					click_data  = $(this).attr("data");

				}else if( $(this).hasClass('js-price') ){
					var price1 = $("input[name=p_min]").val().replace(/,/g, "");
					var price2 = $("input[name=p_max]").val().replace(/,/g, "");

					price1 = (price1.length == 0) ? "0" : price1;
					price2 = (price2.length == 0) ? "0" : price2;

					//入力されているものが数値かどうか調べる
					if( !jQuery.isNumeric(price1) || !jQuery.isNumeric(price2) || parseInt(price1) > parseInt(price2) || parseInt(price2) == 0 ){
						$(".js-price-error").text("値が正しくありません。");
						return;
					}else{
						$(".js-price-error").text("");
					}
					click_param = $(this).attr("data");
					click_data  = price1 + "-" + price2;

					console.log(price1, price2, click_data, click_param);

				}else if( $(this).hasClass('js-price-del') ){
					click_param = "cardprice";
					click_data  = "delete";

				}else{
					click_param = $(this).parents("dl").attr("data");
					click_data  = $(this).attr("data");

				}

				
				
			}else{
				click_param = $(this).attr("class");
				click_data  = $(this).attr("data");

			}

			var url_segment = "";
			maker_segment = $("#param-mvno-string").val();
			if( click_param != "mvno" ){
				now_param['mvno'][0] = "";
				url_segment = maker_segment+"/";

			}
			//createParam( now_param );

			console.log(now_param['mvno']);

			//飛び先のパラメータ作成
			var url_param = now_param;
			
			for( var i=0; i<param_array.length; i++ ){

				if( now_param[param_array[i]][0] != "" ){
					if( click_param == param_array[i] ){
						var array_number = jQuery.inArray( click_data, now_param[param_array[i]] );
						
						if( array_number != -1){
							//すでにある場合
							url_param[param_array[i]].splice(array_number, 1);
							if( url_param[param_array[i]].length == 0 ){
								url_param[param_array[i]][0] = "";
								if( click_param == "mvno" ){
									url_segment = "";
								}
							}
							//メーカが1つの場合はセグメントする
							if( url_param[param_array[i]].length == 1 &&  param_array[i] == "mvno"){
								maker_segment = $('input[name=sim'+url_param[param_array[i]][0]+']').val();
								url_param[param_array[i]][0] = "";
								//url_segment = maker_segment+"/";
							}
							//並び順は1つ
							if( url_param[param_array[i]].length == 1 && ( param_array[i] == "cardsort" || param_array[i] == "carddisplay" || param_array[i] == "cardprice") ){
								url_param[param_array[i]][0] = click_data;
							}
						}else{
							//新規に追加
							if( param_array[i] == "cardsort"  || param_array[i] == "carddisplay" ){
								url_param[param_array[i]][0] = click_data;

							}else if( param_array[i] == "cardprice" ){
								if( click_data == "delete" ){
									url_param[param_array[i]][0] = "";
								}else{
									url_param[param_array[i]][0] = click_data;
								}
							}else{
								url_param[param_array[i]].push(click_data);
								url_param[param_array[i]].sort( function( a, b ){
									if( parseInt(a) < parseInt(b) ) return -1;
									if( parseInt(a) > parseInt(b) ) return 1;
									return 0;
								} );
							}
						}
					}
				}else{

					if( click_param == param_array[i] ){
						if( click_param != "mvno" ){
							url_param[param_array[i]][0] = click_data;
						}else{
							url_segment = maker_segment+"/";
						}
					}
				}
			}

			var result_param = "";
			var param_connect = "?";
			for( key in url_param ){
				if( url_param[key][0] != ""){
					var param_val = url_param[key].join().replace(/,/g, "_");
					result_param = result_param + param_connect + key + "=" + param_val;
					param_connect = "&";
				}
			}
			//return;
			location.href = "/simcard/" + url_segment + result_param;

		}else{
			//パラメータがない状態で選ばれたとき
			var param_type = "";
			var param_data = "";
			if( $(this).hasClass('js-order') || $(this).hasClass('js-disp') ){
				param_type = $(this).parent("ul").attr("data");
				param_data  = $(this).attr("data");

			}else if( $(this).hasClass('js-price') ){
				var price1 = $("input[name=p_min]").val().replace(/,/g, "");
				var price2 = $("input[name=p_max]").val().replace(/,/g, "");

				price1 = (price1.length == 0) ? "0" : price1;
				price2 = (price2.length == 0) ? "0" : price2;

				//入力されているものが数値かどうか調べる
				if( !jQuery.isNumeric(price1) || !jQuery.isNumeric(price2) || parseInt(price1) > parseInt(price2) || parseInt(price2) == 0 ){
					$(".js-price-error").text("値が正しくありません。");
					return;
				}else{
					$(".js-price-error").text("");
				}
				param_type = $(this).attr("data");
				param_data  = price1 + "-" + price2;

			}else if( $(this).hasClass('js-price-del') ){
				return;

			}else{
				param_type = $(this).parents("dl").attr("data");
				param_data  = $(this).attr("data");

			}

			

			//最初のメーカーを選んだ場合は専用ページへ
			if( param_type != "mvno" ){
				location.href = "/simcard/?" + param_type + "=" + param_data;
			}else{
				param_data = $(this).next("input").val();
				location.href = "/simcard/" + param_data + "/";
			}

		}

	} );
});