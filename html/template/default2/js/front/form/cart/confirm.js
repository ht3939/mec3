/*
# カート共通のJavaScript
*/
;(function($){
	'use strict';

	/*
	## 支払い方法のアコーディオン

	### 使い方
	以下のように、クラスを付与する。

	.js-payment-method-accordion
		.js-payment-method-accordion-handle[href="#アコーディオンの対象となるボックスのID名"][data-payment-method-accordion-reverse-text="ON, OFF時で切替えるテキスト"]
		#アコーディオンの対象となるボックスのID名
	*/
	$(function(){
		var $doc = $(document);

		//読込み時に、アコーディオンの対象となるボックスを非表示にする
		// $('.js-payment-method-accordion-target').hide();

		$doc.on('click', '.js-payment-method-accordion-handle', function(){
			var $this = $(this);
			var $target = $this.closest('.js-payment-method-accordion-group').find('.js-payment-method-accordion-target');

			if(!$this.hasClass('active')){
				$this.addClass('active');
				$target.stop().slideDown(500);
			}
			else{
				$this.removeClass('active');
				$target.stop().slideUp(500);
			}

			var $reverseText = $this.data('payment-method-accordion-reverse-text');
			if($reverseText !== undefined){
				var $text = $this.text();
				$this.text($reverseText);
				$this.data('payment-method-accordion-reverse-text', $text);
			}

		});

	});



})(jQuery);