/*
# カート共通のJavaScript
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


})(jQuery);