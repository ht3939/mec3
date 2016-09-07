$(document).ready(function(){

	var getList = function( param ){
		$.ajax({
			type : "POST",
			url  : "/server/getList.php",
			data : param,
			beforeSend : function(){
				$("#sp-item-list").hide().empty();
			},
			success : function( res ){
				$("#sp-item-list").append(res).fadeIn(function(){
					//ボックスの高さを揃える
					//デザイン側で定義してる関数
				});

				var result_num = $("input[name=result_num]").val();
				$(".device-num").html(result_num);
			},
			error : function(){
				console.log("error");
			},
			complete : function(){
				// setTimeout(function(){
				// 	equalizeBoxHeight();
				// }, 1000);
			}
		});
	};

	function getParam(){

		var param = "";

		//端末メーカー
		var param = "brand=";
		var brand_id = [];
		var brand = $(".js-brand.active");
		for(var i=0; i<brand.length; i++){
			if(i!=0) param = param+",";
			param = param + $(brand[i]).attr("data");
		}

		//端末カテゴリ
		var param = param+"&category=";
		var category_id = [];
		var category = $(".js-cate.active");
		for(var i=0; i<category.length; i++){
			if(i!=0) param = param+",";
			param = param + $(category[i]).attr("data");
		}

		//端末カラー
		var param = param+"&color=";
		var checkedColor = [];
		var color_param = "";
		$(".js-color.active").each( function(){
			color_param += $(this).children('span').attr("data") + ",";
		});
		color_param = color_param.slice(0, -1);
		param = param + color_param;

		//価格帯
		var param = param+"&min_price=";
		var min_price = $("input[name=p_min]").val();
		param = param + min_price;

		var param = param+"&max_price=";
		var max_price = $("input[name=p_max]").val();
		param = param + max_price;

		//並び順
		var param = param+"&order=";
		var order = $(".js-order.active").attr("data");
		param = param + order;

		return param;
	}

	//現在の絞込み条件追加
	function conditionCreate( elem, varsion ){

		var classString = elem.html().toLowerCase().replace(/ /g, '');
		var data = elem.attr("data");
		var type = elem.attr("class").replace(/ active/g, "");

		if(varsion == "add"){
			$(".js-condition").append('<li class="'+classString+'" type="'+type+'"data="'+data+'" >'+elem.html()+'</li>');
			$("."+classString).bind("click", function(){
				$("[data="+$(this).attr("data")+"]."+$(this).attr("type")).removeClass('active');
				$(this).remove();
				if( $(".js-condition").find("li").length == 0){
					$(".js-condition").append('<li class="none">なし</li>');
				}else{
					$(".js-condition").find("li.none").remove();
				}
				var param = getParam();
				getList( param );
			});
		}else if (varsion == "remove"){
			$(".js-condition").find("li."+classString+"").remove();

		}

		if( $(".js-condition").find("li").length == 0){
			$(".js-condition").append('<li class="none">なし</li>');
		}else{
			$(".js-condition").find("li.none").remove();
		}
	}

	//ロード時
	function init(){

		var maker = $("#maker-limit").val();
		if( maker != "none"){

			conditionCreate($(".brand"+maker), "add");
			$(".brand"+maker).addClass('active');
		}else{
			$(".js-condition").append('<li class="none">なし</li>');
		}


		var initParam = getParam();
		getList( initParam );
	}

	init();

	$(".js-brand").on( "click", function(){
		if($(this).hasClass('active')){
			$(this).removeClass('active');
			conditionCreate($(this), "remove");
		}else{
			$(this).addClass('active');
			conditionCreate($(this), "add");
		}

		var param = getParam();
		getList( param );
	});

	$(".js-cate").on( "click", function(){
		if($(this).hasClass('active')){
			$(this).removeClass('active');
			conditionCreate($(this), "remove");
		}else{
			$(this).addClass('active');
			conditionCreate($(this), "add");
		}

		var param = getParam();
		getList( param );
	});

	$(".js-order").on("click" ,function(){
		$(".js-order").removeClass("active");
		$(this).addClass('active');

		var param = getParam();
		getList( param);
	});

	$("li.js-color").on("click" ,function(){

		if($(this).hasClass('active')){
			$(this).removeClass('active');
		}else{
			$(this).addClass('active');
		}

		var param = getParam();
		getList( param);
	});

	$(".submit").on( "click", function(){
		var param = getParam();
		getList( param);
	});

	$("input[type=reset]").on("click", function(){
		$("input[name=p_min]").val(0);
		$("input[name=p_max]").val(0);
		var param = getParam();
		getList( param);
	});

	$("a.disabled").click(function(e){
		e.preventDefault();
	});

});