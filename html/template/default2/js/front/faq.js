$(document).ready(function(){
	
	//よくある質問の開閉
	$("#container .faq dd").hide();
	$("#container .faq dd.default").show();
	$("#container .faq dt a").click(function(){
		$(this).toggleClass("a_active");
		$(this).parents(".faq").children("dd").slideToggle();
	});
});
