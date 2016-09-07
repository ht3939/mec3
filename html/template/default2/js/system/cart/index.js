$(function(){

	var changeCart = function( param ){
		$.ajax({
			type : "POST",
			url  : $("input[name=BASE_URL]").val()+"API/cart.php",
			data : param,
			beforeSend : function(){

			},
			success : function( res ){
				switch(res){
					case "delete":
						location.reload();
						break;
					case "change" :
						//priceChange();
						break;
					default:
						break;
				}
			},
			error : function(){
				console.log("error");
			},
		});
	}

	//削除ボタンクリック時
	$(".js-delete-btn").on("click", function( event ){
		event.preventDefault();
		var param = "func=delete";
		var param = param + "&name_id="+$(this).parents("tr").find("input[name=name_id]").val();
		changeCart( param );
	});

	//変更ボタンクリック時
	$(".js-change-btn").on("click", function( event ){
		//event.preventDefault();
		var param = "func=change";
		var param = param + "&name_id="+$(this).parents("tr").find("input[name=name_id]").val();
		changeCart( param );
	});

});