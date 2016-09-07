;(function($){
		//ヘッダーのカートボタンのホバー時のアニメーション
		;(function(){
			$('.js-cart-hover-btn').on("mouseover", function(){
				$(this).stop().animate({
					 top: '11px'
				}, 400 ,'easeOutElastic');
			});
			$('.js-cart-hover-btn').on("mouseout", function(){
				$(this).stop().animate({
					 top: '0'
				}, 400 ,'easeOutElastic');
			});
		})();


		;(function(){
			// URLによってナビゲーションにactiveクラスを付与
			var path = location.pathname;
			if(path.match(/^\/$/)){
				$(".js-navi1").addClass("active");
			}
			else if(path.match(/simfree-sumaho/)){
				$(".js-navi2").addClass("active");
			}
			else if(path.match(/simcard/)){
				$(".js-navi3").addClass("active");
			}
			else if(path.match(/guide/)){
				$(".js-navi4").addClass("active");
			}
		})();

		//------------------ ▼ スムーススクロール ▼--------------------
		;(function(){
			$('.js-scroll').click(function(event){
				var _this = $(this);
				//スクロール位置調整値を取得する
				var offset = _this.data('scroll-offset');
				if(offset === undefined) { offset = 0; }

				var href = _this.attr("href");
				var target = $(href == "#" || href == "" ? 'html' : href);
				var position = target.offset().top + offset;
				$("html, body").animate({scrollTop: position}, 500);
			});
		})();

})(jQuery);
// ;(function($){
// 	'use strict';
// 	$(function(){
// 		//ユーザエージェント文字列を取得する
// 		var ua = navigator.userAgent;



// 		;(function(){
// 			// URLによってナビゲーションにactiveクラスを付与
// 			var path = location.pathname;
// 			if(path.match(/^\/$/)){
// 				$("#navi navi-list").addClass("active");
// 			}
// 			else if(path.match(/plan/)){
// 				$("#navi navi-list").addClass("active");
// 			}
// 			else if(path.match(/smartphone/)){
// 				$("#navi navi-list").addClass("active");
// 			}
// 			else if(path.match(/simcard/)){
// 				$("#navi navi-list").addClass("active");
// 			}
// 			else if(path.match(/campaign/)){
// 				$("#navi navi-list").addClass("active");
// 			}
// 			else if(path.match(/guide/)){
// 				$("#navi navi-list").addClass("active");
// 			}
// 		})();



// 		//------------------ ▼ ページトップへ ▼--------------------
// 		// ;(function(){
// 		// 	$(".scroll").click(function(event){
// 		// 		event.preventDefault();

// 		// 		var url = this.href;
// 		// 		var parts = url.split("#");
// 		// 		var target = parts[1];
// 		// 		var target_offset = $("#"+target).offset();
// 		// 		var target_top = target_offset.top;

// 		// 		$('html, body').animate({scrollTop:target_top}, 1000);
// 		// 	});
// 		// })();

// 		//------------------ ▼ スムーススクロール ▼--------------------
// 		;(function(){
// 			$('.js-scroll').click(function(event){
// 				var _this = $(this);
// 				//スクロール位置調整値を取得する
// 				var offset = _this.data('scroll-offset');
// 				if(offset === undefined) { offset = 0; }

// 				var href = _this.attr("href");
// 				var target = $(href == "#" || href == "" ? 'html' : href);
// 				var position = target.offset().top + offset;
// 				$("html, body").animate({scrollTop: position}, 500);
// 			});
// 		})();








// 		//スマホで閲覧時にFDをリンクにする
// 		// ;(function(){
// 		//
// 		// 	if(ua.indexOf('iPhone') === -1 && !(ua.indexOf('Android') > -1 && ua.indexOf('Mobile') > -1)){
// 		// 		return false;
// 		// 	}
// 		//
// 		// 	$('.fd-area, #contact-area').find('.number').each(function(){
// 		// 		$(this).wrap('<a href="tel:' + $(this).text() + '"></a>');
// 		// 	});
// 		//
// 		// })();

// 		//PCの追従フッター
// 		// ;(function(){
// 		// 	var followFooter = $('#js-pc-follow-footer');//追従フッターのボックス
// 		// 	var startPoint = $('#js-pc-follow-footer-start-point');//開始位置のボックス
// 		// 	var endPoint = $('#js-pc-follow-footer-end-point');//終了位置のボックス
// 		// 	if(!followFooter.length || !startPoint.length || !endPoint.length){ return false; }

// 		// 	//スマホ閲覧時は隠す
// 		// 	if(ua.indexOf('iPhone') > -1 || ua.indexOf('Android') > -1 && ua.indexOf('Mobile') > -1){
// 		// 		followFooter.hide();
// 		// 		return false;
// 		// 	}

// 		// 	//追従フッターの初期表示位置を設定する
// 		// 	var followFooterHeight = followFooter.outerHeight();
// 		// 	followFooter.css({
// 		// 		'bottom'					: followFooterHeight * (-1)
// 		// 	});

// 		// 	$("#js-pc-follow-footer .btn-hide").click(function(){
// 		// 		$(this).hide();
// 		// 		$('#js-pc-follow-footer .btn-inquiry').show();
// 		// 		followFooter.animate({
// 		// 			'bottom'		: followFooterHeight * (-1)
// 		// 		});
// 		// 		followFooter.addClass('follow-hide');
// 		// 	});
// 		// 	$('#js-pc-follow-footer .btn-inquiry').click(function(){
// 		// 		$(this).hide();
// 		// 		$("#js-pc-follow-footer .btn-hide").show();
// 		// 		followFooter.animate({
// 		// 			'bottom'		: 0
// 		// 		});
// 		// 		followFooter.removeClass('follow-hide');
// 		// 	});

// 		// 	var win = $(window);
// 		// 	var timerId;
// 		// 	win.on('load scroll resize', function(){
// 		// 			if(timerId !== undefined){ clearTimeout(timerId); }
// 		// 			timerId = setTimeout(function(){
// 		// 				//追従フッター表示条件
// 		// 				if(startPoint.offset().top + startPoint.outerHeight() < win.scrollTop() && win.scrollTop() + win.height() < endPoint.offset().top){
// 		// 					if(!followFooter.hasClass('follow-hide')){
// 		// 						followFooter.stop().animate({
// 		// 							'bottom'		: 0
// 		// 						});
// 		// 						followFooter.find('.follow-btn').stop(true,false).fadeIn('fast');
// 		// 					}else{
// 		// 						followFooter.find('.follow-btn').stop(true,false).fadeIn('fast');
// 		// 					}
// 		// 				//追従フッター非表示条件
// 		// 				}else{
// 		// 					if(!followFooter.hasClass('follow-hide')){
// 		// 					followFooter.stop().animate({
// 		// 						'bottom'		: followFooterHeight * (-1)
// 		// 					},function(){
// 		// 						followFooter.find('.follow-btn').fadeOut('fast');
// 		// 					});
// 		// 					}else{
// 		// 						followFooter.stop().animate({
// 		// 							'bottom'		: followFooterHeight * (-1)
// 		// 						},function(){
// 		// 							followFooter.find('.follow-btn').fadeOut('fast');
// 		// 						});
// 		// 					}
// 		// 				}
// 		// 			}, 100);
// 		// 	});
// 		// })();

// 		//スマホで閲覧時に画面下部にFDを追従させる
// 		//拡大時は非表示にする
// 		// ;(function(){

// 		// 	if(ua.indexOf('iPhone') === -1 && !(ua.indexOf('Android') > -1 && ua.indexOf('Mobile') > -1)){
// 		// 		return false;
// 		// 	}

// 		// 	var win = $(window);
// 		// 	var doc = $(document);
// 		// 	var followFooter = $('#js-follow-footer');
// 		// 	followFooter
// 		// 	.css({
// 		// 		display			: 'block',
// 		// 		bottom			: followFooter.outerHeight()*(-1) + 'px'
// 		// 	});
// 		// 	var touchTimerId;
// 		// 	var scrollTimerId;
// 		// 	var zoomRate;

// 		// 	//FDを表示する
// 		// 	followFooter.animate({
// 		// 		bottom			: '0px'
// 		// 	}, 300);

// 		// 	$('body').css({
// 		// 		paddingBottom	: followFooter.outerHeight()
// 		// 	});

// 		// 	//伸縮率を更新する
// 		// 	doc.on('touchend', function(){
// 		// 		clearTimeout(touchTimerId);
// 		// 		touchTimerId = setTimeout(function(){
// 		// 			zoomRate = Math.round(document.body.clientWidth / window.innerWidth * 100) / 100;
// 		// 		}, 100);
// 		// 	});

// 		// 	//伸縮状態を取得する
// 		// 	//拡大時にtrue、拡大していないときにfalseが返る
// 		// 	var getZoomState = function(){
// 		// 		return 1 < zoomRate ? true : false;
// 		// 	};

// 		// 	win.scroll(function(){

// 		// 		clearTimeout(scrollTimerId);
// 		// 		scrollTimerId = setTimeout(function(){

// 		// 			//非拡大時、および、開始地点～終了地点間にいる場合のみ、画面固定FDを表示する
// 		// 			if(!getZoomState()){
// 		// 				//表示する

// 		// 				followFooter
// 		// 				.stop()
// 		// 				.animate({bottom : '0px'}, 300);

// 		// 			}
// 		// 			else{
// 		// 				//隠す

// 		// 				followFooter
// 		// 				.stop()
// 		// 				.animate({bottom : followFooter.outerHeight()*(-1) + 'px'}, 300);

// 		// 			}

// 		// 		}, 200);

// 		// 	});

// 		// })();



// 		//スマホで閲覧時に画面下部に追従するFDを押すと、ポップアップを表示する
// 		// ;(function(){

// 		// 	$('#planlist').click(function(){
// 		// 		$('#entry-popup-bg,#entry-popup').fadeIn();
// 		// 		$('#entry-popup').css({
// 		// 			'top': '15%',
// 		// 			'left': '2%',
// 		// 			'position': 'fixed'
// 		// 		})
// 		// 	});
// 		// 	$('#entry-popup-bg,#entry-popup').click(function(){
// 		// 		$('#entry-popup-bg,#entry-popup').fadeOut();
// 		// 	});

// 		// })();



// 	});
// })(jQuery);
