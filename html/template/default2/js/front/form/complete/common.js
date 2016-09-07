/*
# Web完結フォームで共有するJavaScript
*/
;(function($){
	'use strict';
	$(function(){


		/*
		## 追従サイドバー
		*/
		;(function(){
			//最低限必要な要素を取得する
			var win = $(window);
			var doc = $(document);
			var followSide = $('#js-side-right');//追従サイドバーのボックス
			var followSideOuter = $('#js-side-right-outer');//追従サイドバーのすぐ上の親ボックス（これには何もスタイルをあててはいけない）
			var main = $('#js-contents-left');//サイドバーとは対となるメインコンテンツのボックス
			if(
				    !followSide.length
				 || !followSideOuter.length
				 || !main.length
			){
				return false;
			}

			//
			followSideOuter.css({
				'position'			: 'relative'
			});

			//必要な初期値を退避する
			var followSideDefaultCssProperty = {
				'position'			: followSide.css('position'),
				'top'				: followSide.css('top'),
				'left'				: followSide.css('left')
			}

			//追従サイドバーの横幅を確保する
			followSide.width(followSide.width());

			//基準サイズのボックスの横幅を設定する
			var baseContainerWidth = 1000;
			//追従サイドバーの上方向の余白を設定する
			var followSideTop = 30;
			//基準サイズのボックスの左端から、追従サイドバーの左端までの間隔を取得する
			var followSideLeft = baseContainerWidth - followSide.width();


			//直近のposition: relative;を持った親要素を取得する
			function getRecentPosRelativeParent(_mine){
				var parent = _mine.parent();
				if(parent.css('position') === 'relative'){
					return parent;
				}
				else if(parent.get(0).tagName === 'BODY'){
					return $('body');
				}
				else{
					return getRecentPosRelativeParent(parent);
				}
			}

			win.on('load resize scroll', function(){
				//再取得が必要な値の取得
				var followSideHeight = followSide.height();//追従サイドバーの高さ
				var startPointY = followSideOuter.offset().top - followSideTop;//追従開始位置
				var endPointY = main.offset().top + main.outerHeight();//追従終了位置
				var recentPosRelativeParent = getRecentPosRelativeParent(followSide);//追従サイドバーの自分は除いた直近のrelative要素

				//特定の範囲で追従させ、特定の位置で止める
				if(win.scrollTop() < startPointY){
					//通常時
					followSide.css({
						'position'				: followSideDefaultCssProperty.position,
						'top'					: followSideDefaultCssProperty.top,
						'left'					: followSideDefaultCssProperty.left
					});
				}
				else{
					if(win.scrollTop() < endPointY - followSideTop - followSideHeight){
						//追従時

						//横スクロール
						var left =
						win.width() < baseContainerWidth
						? 0 < win.scrollLeft()
							? followSideLeft - win.scrollLeft()
							: followSideLeft
						: (win.width() - baseContainerWidth) / 2 + followSideLeft;

						followSide.css({
							'position'				: 'fixed',
							'top'					: followSideTop + 'px',
							'left'					: left + 'px'
						});

					}
					else{
						//ページ下部固定時
						followSide.css({
							'position'				: 'absolute',
							'top'					: endPointY - followSideHeight - recentPosRelativeParent.offset().top + 'px',
							'left'					: '0px'
						});
					}

				}

			});

		})();


	});
})(jQuery);