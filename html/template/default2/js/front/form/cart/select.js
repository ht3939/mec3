/*
# カートの製品情報選択ページのJavaScript
*/
;(function($){
	'use strict';

	/*
	## MNPのアコーディオン
	*/
	$(function(){
		var $doc = $(document);

		//ページが読み込まれた時に、MNPのラジオボタンがチェックされていたら、MNPの入力欄を表示する。
		$doc.on('ready', function(){
			$('.js-radio-block-input--is-mnp').each( function(){
				var $detail = $(this).closest(".product-item-value").children(".js-mnp-detail");
				if($(this).prop('checked') === true){
					$detail.stop().slideDown();
				}
				
			});
		});

		//MNPのラジオボタンがチェックされた時に、MNPの入力欄を表示する。
		$doc.on('click', '.js-radio-block-input--contract', function(){
			var $this = $(this);
			//closest("dd") 先祖のうち一番近いdd要素を取得する
			var $detail = $this.closest(".product-item-value").children(".js-mnp-detail");

			if($this.hasClass('js-radio-block-input--is-mnp') === true){
				$detail.stop().slideDown();
			}
			else{
				$detail.stop().slideUp();
			}
		});

	});



})(jQuery);