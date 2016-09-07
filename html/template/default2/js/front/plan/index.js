/*
# 料金プランのJavaScriptファイルです。
*/
;(function($){
	'use strict';
	$(function(){

		/*
		## 任意の位置へスクロールする
		* 使い方:
		* 1. href属性にスクロール先のハッシュを設定する （<a href="#header">）
		* 2. 1で設定した要素にscrollクラスを設定する （<a href="#header" class="course-scroll">）
		*/
		;(function(){
			var scroll = $('.course-scroll');

			scroll.click(function(e){
				e.preventDefault();
				var scrollPoint = $('#' + this.href.split('#')[1]).offset().top - 30;
				$('html, body').animate({scrollTop:scrollPoint}, 500);
			});

		})();




	});
})(jQuery);