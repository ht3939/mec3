/*
# 詳細ページ用のJavaScriptです。
何詳細？
*/
;(function($){
	'use strict';


	// MOREを押すと、サムネイルの次のページを表示
	$(function(){
		//ロード時
		function init(){
			$('.js-next-btn').each(function(){
				$(this).siblings('.js-image-list').eq(0).show();
				$(this).siblings('.js-image-list').eq(0).children("li").eq(0).addClass("active");
			});
		}

		init();

		$('.js-next-btn').each(function(){
			var changeTarget = $(this).siblings('.js-image-list');
			var listLength = changeTarget.length;
			var i = 0;
			$(this).click(function(){
				changeTarget.hide();
				if(listLength-1 > i){
					i ++;
					changeTarget.eq(i).fadeIn();
				}else{
					i = 0 ;
					changeTarget.eq(0).fadeIn();
				}
			});
		});
	});

	//色のチェック時に各箇所のコンテンツ切り替え
	$(function(){
		//ロード時
		function init(){
			$(".js-color:first").addClass('active');
			$(".js-color-changed").each(function(){
				$(this).children().eq(0).show();
			});
		}

		init();

		$(".js-color").on("click", function(){
			colorChange($(this).index());
		});

		$("#header-item-color").on("change", function(){
			colorChange($(this).val());
		});

		function colorChange(colorIndex){
			$(".js-color").removeClass('active');
			$(".js-color").eq(colorIndex).addClass('active');
			$(".js-color-changed").each(function(){
				$(this).children().hide();
				$(this).children().eq(colorIndex).fadeIn();
				//main画像の変更
				var target = $(this).children().eq(colorIndex).find(".js-image-list").children("li");
				target.removeClass('active');
				target.eq(0).addClass('active');
				var _src = target.eq(0).find('img').attr('src');
				$('.main-img img').hide().attr('src', _src).fadeIn();

			});
			$("#header-item-color").val(colorIndex);
		}
	});

	//cggs注記用のモーダルウィンドウ
	$('.js-cggs-modalwindow-handle').colorbox({
		inline: true,
		className: 'cggs',
		width: 500,
		maxWidth: '90%',
		maxHeight: '90%',
		onLoad: function(){
			$('#cboxClose').hide();
		},
		onComplete: function(){
			$('#cboxClose').fadeIn('fast');
		},
		onClosed: function(){
			$('#cboxClose').hide();
		}
	})

	//---商品数量の連動
	$(function(){
		$("#header-item-num,#i_item_num").on("change",function(){
			itemNumChange($(this).val());
		});
		function itemNumChange(num){
			$("#header-item-num").val(num);
			$("#i_item_num").val(num);
		}
	});

	// 色を選択するとクリックした要素のクラスによって、端末のディレクトリ名を変更
	$('.side-item-color li span').click(function(){

		// IDと色を紐付け
		var colorClass = $(this).attr('class');
		var imgDir = '/img/smartphone/detail/';
		var imgName = 'item-dtl-sim-1.png'
		//var src = imgDir + deviceName + colorClass + '/' + imgName;

		//console.log(colorClass);
		//$('.main-img img').attr('src', src);
	});

	$(function(){
		$('.detail-img-list').each(function(){
			$(this).children('li').click(function(){
				if(!$(this).hasClass('item-more')){
					$(this).parent('.detail-img-list').children('li').removeClass('active');
					var src = $(this).find('img').prop('src');
					$('.main-img img').hide().attr('src', src).fadeIn();
					$(this).addClass('active');
				}else{
					location.href = $(this).find('a').prop('href');
				}
			});

		});
	});


	/*
	## ラジオボタンのチェック
	*/
	$(function(){
		var $doc = $(document);

		//ページが読み込まれた時に、チェックされたラジオボタンを元に選択状態を設定する。
		$doc.on('ready', function(){
			$('.js-radio-block-input:checked').closest('.js-radio-block-active').addClass('active');
		});

		//ラジオボタンがチェックされた時に、対応したボックスを選択状態にする。
		$doc.on('click', '.js-radio-block-input', function(){
			var $this = $(this);
			$this.closest('.js-radio-block-group').find('.js-radio-block-active').removeClass('active');
			$this.closest('.js-radio-block-active').addClass('active');
		});

		//支払い方法を選択した場合の切り替え
		$doc.on('click', '.js-radio-block-active', function(){
			var $this = $(this);
			var tabclick = $this.closest('.js-radio-block-group').find('li');
			var num = tabclick.index(this);
			var tabcontents = $this.closest('.js-select-payment').find('.js-payment-price-switch-contents');

			tabcontents.removeClass('active');
			tabcontents.eq(num).addClass('active');
			tabclick.removeClass('active');
			$this.addClass('active');

		});
	});



	$(function() {
		$('#' + $('#item-dtl-tab li.active').data('tab')).show();
		$('#item-dtl-tab li').click(function(){
		console.log('test');
			if(!$(this).hasClass('active')){
				$('#item-dtl-tab li').removeClass('active');
				$(this).addClass('active');
				$('.item-dtl-tab-cont').hide();
				$('#'+$(this).data('tab')).fadeIn();
			}
		});
		$('#' + $('#item-dtl-purchase-tab li.active').data('tab')).show();
		$('#item-dtl-purchase-tab li').click(function(){
			if(!$(this).hasClass('active')){
				$('#item-dtl-purchase-tab li').removeClass('active');
				$(this).addClass('active');
				$('.item-dtl-purchase-tab-cont').hide();
				$('#'+$(this).data('tab')).fadeIn();
			}
		});
	});



	//pagerクリック時の対応
		if(window.location.search.indexOf("page") == 1){
			$('#item-dtl-tab li').removeClass('active');
			$('#item-dtl-tab li[data-tab=review]').addClass('active');
			$('.item-dtl-purchase-tab-cont').hide();
			$('#'+$('#item-dtl-tab li[data-tab=review]').data('tab')).show();
			$(window).on('load',function(){
				$(window).scrollTop(($('#item-dtl-tab').offset().top)-($('#follow-header').height()));
			});
		}
	//iphone6時の対応
		if(!(window.location.pathname.indexOf("iphone6") == -1)){
			$('.js-display-change').hide();
			$('.js-display-change2').text('＊ 現ページの「分割払いの場合」は、スマートモバイルコミュニケーションズ（株）が提供する端末セットプランとなります。「一括払いの場合」は、こちらが提供する各社からプランをお選びいただけます。');
		}
	//iphone6,ascend-g6,blade-v6時の対応
	if(!(window.location.pathname.indexOf("iphone6") == -1) || !(window.location.pathname.indexOf("ascend-g6") == -1) || !(window.location.pathname.indexOf("blade-v6") == -1)){
		$('.js-display-change3').text('');
	}


})(jQuery);
