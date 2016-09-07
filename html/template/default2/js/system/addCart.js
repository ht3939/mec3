$(function (){
	var addCart = function( param){

		$.ajax({
			type : "POST",
			url  : $("input[name=BASE_URL]").val()+"API/cart.php",
			data : param,
			beforeSend : function(){

			},
			success : function( res ){
				$("#addCart").attr("action", "https://"+location.host+$("input[name=BASE_URL]").val()+"form-cart/");
				$("#addCart").attr("target", "newwin");
				$("#addCart").submit();

			},
			error : function(){

			},
		});
	}

	function getParamSP(){

		var param = "";
		var param = "item_id="+$("input[name=item-id]").val();
		var param = param + "&func=add";

		if( $("input[name=item-id]").val().indexOf("SP") != -1){
			var param = param+"&color="+$(".js-color.active").find("span").attr("class");
		}else if( $("input[name=item-id]").val().indexOf("SIM") != -1){
			var param = param+"&color=none";
		}

		//var param = param+"&num="+$("select[name=i_item_num]").val();
		return param;
	}


	$("#addCart-divide").on("click", function(e){
		window.open("","newwin");
		e.preventDefault();
		var param = getParamSP();
		var param = param + "&pay_num=divide";
		addCart( param );
	});

	$("#addCart-bundle").on("click", function(e){
		window.open("","newwin");
		e.preventDefault();
		var param = getParamSP();
		var param = param + "&pay_num=bundle";
		addCart( param );
	});

	$(".js-side-btn-cart-sp").on("click", function(e){
		window.open("","newwin");
		e.preventDefault();
		var param = getParamSP();
		var param = param + "&pay_num="+$("input[name=i_contract]:checked").val();
		addCart( param );
	});

	$(".js-into-cart-sim").on("click", function(e){
		window.open("","newwin");
		e.preventDefault();
		var param = getParamSP();
		addCart( param );
	});

	$(".js-matchcart-sim").on("click", function(){
		window.open("","newwin");
		var param = "item_id="+$(this).parent("td").find("input[name=item_id]").val();
		var param = param + "&func=add";
		addCart( param );
	});

	$(".js-matchcart-sp").on("click", function(){
		window.open("","newwin");
		var param = "item_id="+$(this).parent("td").find("input[name=item_id]").val();
		var param = param + "&color="+$(this).parent("td").find("input[name=color_id]").val();;
		var param = param + "&func=add";
		addCart( param );
	});


	//feature用
	$(".feature-sim").on("click", function(e){
		window.open("","newwin");
		e.preventDefault();
		var param = "item_id="+$(this).parents("li").find("input[name=name_id]").val();
		var param = param + "&func=add";
		addCart( param );
	});

	$(".feature-sp").on("click", function(e){
		window.open("","newwin");
		e.preventDefault();
		var param = "item_id="+$(this).parents("li").find("input[name=name_id]").val();
		var param = param + "&color="+$(this).parents("li").find("input[name=color_id]").val();
		var param = param + "&func=add";
		addCart( param );
	});

});