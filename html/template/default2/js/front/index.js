/*
# トップページ用のJavaScriptです。
*/
;(function($){
	'use strict';
	$(function(){


		/*
		## ロゴのスライダー
		*/
		;(function(){
			var appVersion = window.navigator.appVersion.toLowerCase();

			$('#loopslider').each(function(){
				var loopsliderWidth = $(this).width();
				var loopsliderHeight = $(this).height();
				$(this).children('ul').wrapAll('<div id="loopslider_wrap"></div>');

				var listWidth = $('#loopslider_wrap').children('ul').children('li').width();
				var listCount = $('#loopslider_wrap').children('ul').children('li').length;

				var loopWidth = (listWidth)*(listCount);

				$('#loopslider_wrap').css({
					top: '0',
					left: '0',
					width: ((loopWidth) * 3),
					height: (loopsliderHeight),
					overflow: 'hidden',
					position: 'absolute'
				});

				//ie8だと追従フッターの表示に影響を与えるため、ie8以外でスライドする
				if(appVersion.indexOf("msie 8.") === -1){
					$('#loopslider_wrap ul').css({
						width: (loopWidth)
					});
					loopsliderPosition();
				}

				function loopsliderPosition(){
					$('#loopslider_wrap').css({left:'0'});
					$('#loopslider_wrap').stop().animate({left:'-' + (loopWidth) + 'px'},40000,'linear');
					setTimeout(function(){
						loopsliderPosition();
					},40000);
				};

				$('#loopslider_wrap ul').clone().appendTo('#loopslider_wrap')
				$('#loopslider_wrap ul:first').clone().appendTo('#loopslider_wrap');
			});

		})();


	});
})(jQuery);
