//特定のボックスの高さを均一化する
function equalizeBoxHeight(){
	$('.js-flat-height-group').each(function(){
		var _this = $(this);
		var max = 0;
		var target = _this.find('.js-flat-height-target');
		var len = target.length;
		for(var i = 0; i < len; i++){
			var targetHeight = target.eq(i).height();
			console.log(targetHeight);
			if(max < targetHeight){
				max = targetHeight;
			}
		};
		target.height(max);
	});
};
$(window).load(function(){
	equalizeBoxHeight();
});