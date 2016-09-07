/*
# Web完結フォーム Confirmで使用するJavaScript
*/
;(function($){
	'use strict';
	$(function(){

		/*
		## アコーディオン
		*/
		;(function(){
			$('.js-accordion-handle').click(function(){
				var _this = $(this);
				var target = _this.closest('.js-accordion').find('.js-accordion-target');
				var text = _this.text();
				var dataText = _this.data('accordion-text');
				var hasDataText = dataText !== undefined ? true : false;

				target.toggle();
				if(target.css('display') !== 'none'){
					_this.addClass('active');
					if(hasDataText){
						_this.text(dataText);
						_this.data('accordion-text', text);
					}
				}
				else{
					_this.removeClass('active');
					if(hasDataText){
						_this.text(dataText);
						_this.data('accordion-text', text);
					}
				}

			});
		})();

		/*
		## アコーディオン
		*/
		;(function(){
			$('.js-accordion-handle2').click(function(){
				var _this = $(this);
				var target = _this.closest('.js-accordion2').find('.js-accordion-target2');
				var text = _this.text();
				var dataText = _this.data('accordion-text');
				var hasDataText = dataText !== undefined ? true : false;

				target.toggle();
				if(target.css('display') !== 'none'){
					_this.addClass('active');
					if(hasDataText){
						_this.text(dataText);
						_this.data('accordion-text', text);
					}
				}
				else{
					_this.removeClass('active');
					if(hasDataText){
						_this.text(dataText);
						_this.data('accordion-text', text);
					}
				}

			});
		})();



	});
})(jQuery);

