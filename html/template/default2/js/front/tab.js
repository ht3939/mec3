$(function() {
	$('#' + $('#side-tab li.active').data('tab')).show();
	$('#side-tab li').click(function(){
		if(!$(this).hasClass('active')){
			$('#side-tab li').removeClass('active');
			$(this).addClass('active');
			$('.side-tab-cont-inner').hide();
			$('#'+$(this).data('tab')).fadeIn();
		}
	});
});